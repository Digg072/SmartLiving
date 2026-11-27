<?php

namespace frontend\controllers;

use Yii;
use common\models\AgregadoFamiliar;
use yii\filters\AccessControl;
use frontend\models\AdicionarMembroForm;
use common\models\MembroAgregado;
use common\models\User;
use yii\web\ForbiddenHttpException;
use common\models\Despensa;
use common\models\ItemDespensa;
use common\models\MovimentoStock;
use yii\db\Expression;

class AgregadoController extends \yii\web\Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => ['consultarAgregado'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'roles' => ['criarAgregado'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['add-member'],
                        'roles' => ['adicionarMembroAgregado'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['remove-member'],
                        'roles' => ['removerMembroAgregado'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['leave'],
                        'roles' => ['sairAgregado'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update'],
                        'roles' => ['editarAgregado'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => ['eliminarAgregado'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => \yii\filters\VerbFilter::class,
                'actions' => [
                    'remove-member' => ['POST'],
                    'leave' => ['POST'],
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $utilizadorId = (int) Yii::$app->user->id;

        $agregado = AgregadoFamiliar::doUtilizador($utilizadorId);

        $membros = [];
        $historico = [];

        if ($agregado !== null) {

            // Consultar os membros através de ActiveRecord.
            $idsMembros = MembroAgregado::find()
                ->select('utilizador_id')
                ->where(['agregado_id' => $agregado->id]);

            $membros = User::find()
                ->where(['id' => $idsMembros])
                ->orderBy(['username' => SORT_ASC])
                ->all();

            // Obter a despensa familiar.
            $despensa = Despensa::find()
                ->where(['agregado_id' => $agregado->id])
                ->one();

            if ($despensa !== null) {

                // Obter apenas os IDs dos itens desta despensa.
                $itemIds = ItemDespensa::find()
                    ->select('id')
                    ->where(['despensa_id' => $despensa->id])
                    ->column();

                if (!empty($itemIds)) {

                    // Últimos movimentos da despensa familiar.
                    $movimentos = MovimentoStock::find()
                        ->where(['item_id' => $itemIds])
                        ->with(['item', 'utilizador'])
                        ->orderBy([
                            'data_movimento' => SORT_DESC,
                            'id' => SORT_DESC,
                        ])
                        ->limit(10)
                        ->all();

                    // Descobrir quais os primeiros movimentos
                    // dos itens apresentados no histórico.
                    $idsComMovimentos = array_unique(
                        array_map(
                            fn($movimento) => (int) $movimento->item_id,
                            $movimentos
                        )
                    );

                    $primeirosIds = [];

                    if (!empty($idsComMovimentos)) {
                        $primeiros = MovimentoStock::find()
                            ->select([
                                'item_id',
                                'primeiro_id' => new Expression('MIN(id)'),
                            ])
                            ->where(['item_id' => $idsComMovimentos])
                            ->groupBy('item_id')
                            ->asArray()
                            ->all();

                        foreach ($primeiros as $primeiro) {
                            $primeirosIds[(int) $primeiro['item_id']] =
                                (int) $primeiro['primeiro_id'];
                        }
                    }

                    // Preparar os dados para a vista atual.
                    foreach ($movimentos as $movimento) {

                        $item = $movimento->item;
                        $utilizador = $movimento->utilizador;

                        if ($item === null || $utilizador === null) {
                            continue;
                        }

                        $historico[] = [
                            'id_movimento' => $movimento->id,
                            'primeiro_movimento_id' =>
                                $primeirosIds[(int) $movimento->item_id] ?? null,
                            'quantidade_anterior' =>
                                $movimento->quantidade_anterior,
                            'quantidade_nova' =>
                                $movimento->quantidade_nova,
                            'data_movimento' =>
                                $movimento->data_movimento,
                            'nome_item' => $item->nome,
                            'unidade' => $item->unidade,
                            'nome_utilizador' => $utilizador->username,
                        ];
                    }
                }
            }
        }

        return $this->render('index', [
            'agregado' => $agregado,
            'membros' => $membros,
            'historico' => $historico,
        ]);
    }

    public function actionCreate()
    {
        $utilizadorId = (int) Yii::$app->user->id;

        if (AgregadoFamiliar::doUtilizador($utilizadorId) !== null) {
            return $this->redirect(['index']);
        }

        $model = new AgregadoFamiliar();
        $dados = Yii::$app->request->post($model->formName());

        if (is_array($dados)) {
            // Do formulário só aceitamos o nome. O servidor decide quem é o gestor.
            $model->nome = $dados['nome'] ?? null;

            if ($model->criarPara($utilizadorId)) {
                Yii::$app->session->setFlash('success', 'Agregado familiar criado.');
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionAddMember()
    {
        $utilizadorId = (int) Yii::$app->user->id;

        $agregado = AgregadoFamiliar::doUtilizador($utilizadorId);

        if (
            $agregado === null ||
            (int) $agregado->gestor_id !== $utilizadorId
        ) {
            throw new ForbiddenHttpException(
                'Apenas o gestor do agregado pode adicionar membros.'
            );
        }

        $model = new AdicionarMembroForm();

        if (
            $model->load(Yii::$app->request->post()) &&
            $model->adicionar((int) $agregado->id)
        ) {
            Yii::$app->session->setFlash(
                'success',
                'Membro adicionado ao agregado familiar.'
            );

            return $this->redirect(['index']);
        }

        return $this->render('add-member', [
            'model' => $model,
            'agregado' => $agregado,
        ]);
    }

    public function actionRemoveMember($id)
    {
        $gestorId = (int) Yii::$app->user->id;

        $agregado = AgregadoFamiliar::doUtilizador($gestorId);

        if (
            $agregado === null ||
            (int) $agregado->gestor_id !== $gestorId
        ) {
            throw new ForbiddenHttpException(
                'Apenas o gestor pode remover membros.'
            );
        }

        if ($agregado->retirarMembro((int) $id)) {
            Yii::$app->session->setFlash(
                'success',
                'Membro removido do agregado familiar.'
            );
        } else {
            Yii::$app->session->setFlash(
                'error',
                'Não foi possível remover o membro.'
            );
        }

        return $this->redirect(['index']);
    }

    public function actionLeave()
    {
        $utilizadorId = (int) Yii::$app->user->id;

        $agregado = AgregadoFamiliar::doUtilizador($utilizadorId);

        if ($agregado === null) {
            return $this->redirect(['index']);
        }

        if ((int) $agregado->gestor_id === $utilizadorId) {
            throw new ForbiddenHttpException(
                'O gestor não pode sair do agregado familiar.'
            );
        }

        if ($agregado->retirarMembro($utilizadorId)) {
            Yii::$app->session->setFlash(
                'success',
                'Saíste do agregado familiar. Voltaste à tua despensa pessoal.'
            );
        } else {
            Yii::$app->session->setFlash(
                'error',
                'Não foi possível sair do agregado familiar.'
            );
        }

        return $this->redirect(['index']);
    }

    public function actionUpdate()
    {
        $utilizadorId = (int) Yii::$app->user->id;

        $agregado = AgregadoFamiliar::doUtilizador($utilizadorId);

        if (
            $agregado === null ||
            (int) $agregado->gestor_id !== $utilizadorId
        ) {
            throw new \yii\web\ForbiddenHttpException(
                'Apenas o gestor pode editar o agregado familiar.'
            );
        }

        if (Yii::$app->request->isPost) {
            $dados = Yii::$app->request->post($agregado->formName(), []);

            $agregado->nome = isset($dados['nome']) && is_string($dados['nome'])
                ? trim($dados['nome'])
                : '';

            if ($agregado->save()) {
                Yii::$app->session->setFlash(
                    'success',
                    'Nome do agregado familiar atualizado com sucesso.'
                );

                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $agregado,
        ]);
    }

    public function actionDelete()
    {
        $utilizadorId = (int) Yii::$app->user->id;

        $agregado = AgregadoFamiliar::doUtilizador($utilizadorId);

        if (
            $agregado === null ||
            (int) $agregado->gestor_id !== $utilizadorId
        ) {
            throw new \yii\web\ForbiddenHttpException(
                'Apenas o gestor pode eliminar o agregado familiar.'
            );
        }

        try {
            if ($agregado->eliminarComDados()) {
                Yii::$app->session->setFlash(
                    'success',
                    'Agregado familiar eliminado com sucesso.'
                );
            } else {
                Yii::$app->session->setFlash(
                    'error',
                    'Não foi possível eliminar o agregado familiar.'
                );
            }
        } catch (\Throwable $e) {
            Yii::error($e, __METHOD__);

            Yii::$app->session->setFlash(
                'error',
                'Ocorreu um erro ao eliminar o agregado familiar.'
            );
        }

        return $this->redirect(['index']);
    }
}
