<?php

namespace frontend\controllers;

use Yii;
use common\models\Categoria;
use common\models\Despensa;
use common\models\ItemDespensa;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class ItemController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['view'],
                        'roles' => ['consultarDespensa'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create', 'update'],
                        'roles' => ['gerirItens'],
                    ],
                ],
            ],
        ];
    }


    public function actionCreate()
    {
        $despensa = Despensa::obterAtivaPara(
            (int) Yii::$app->user->id
        );

        if ($despensa === null) {
            throw new NotFoundHttpException(
                'Despensa ativa não encontrada.'
            );
        }

        $model = new ItemDespensa();
        $model->despensa_id = $despensa->id;

        $avisoDuplicado = null;

        if ($model->load(Yii::$app->request->post())) {

            // Normalizar espaços do nome apresentado
            $model->nome = trim(
                preg_replace(
                    '/[\s\p{Z}]+/u',
                    ' ',
                    (string) $model->nome
                ) ?? (string) $model->nome
            );

            // Nunca confiar na despensa enviada pelo formulário
            $model->despensa_id = $despensa->id;

            if ($model->validate()) {

                $resultado = ItemDespensa::adicionarOuAcumular(
                    $model,
                    (int) Yii::$app->user->id
                );

                if ($resultado['estado'] === 'incompativel') {

                    $existente = $resultado['item'];

                    // Corrigir automaticamente os campos do formulário
                    $model->nome = $existente->nome;
                    $model->unidade = $existente->unidade;
                    $model->categoria_id = $existente->categoria_id;

                    if ($resultado['unidadeDiferente']) {
                        $model->addError(
                            'unidade',
                            'A unidade era diferente da existente. Foi corrigida.'
                        );
                    }

                    if ($resultado['categoriaDiferente']) {
                        $model->addError(
                            'categoria_id',
                            'A categoria era diferente da existente. Foi corrigida.'
                        );
                    }

                    $avisoDuplicado =
                        'O produto "' . $existente->nome .
                        '" já existe na despensa. A unidade e a categoria ' .
                        'foram ajustadas aos valores existentes. ' .
                        'Confirma os dados e clica novamente em Guardar Item.';

                } else {

                    if ($resultado['estado'] === 'acumulado') {
                        $mensagem =
                            'Produto já existente: quantidade adicionada com sucesso.';

                        if ($resultado['validadeDiferente']) {
                            $mensagem .=
                                ' A validade original foi mantida.';
                        }
                    } else {
                        $mensagem = 'Item criado com sucesso.';
                    }

                    Yii::$app->session->setFlash(
                        'success',
                        $mensagem
                    );

                    return $this->redirect(['/despensa/index']);
                }
            }
        }

        $categorias = \yii\helpers\ArrayHelper::map(
            Categoria::find()
                ->orderBy(['nome' => SORT_ASC])
                ->all(),
            'id',
            'nome'
        );

        return $this->render('create', [
            'model' => $model,
            'categorias' => $categorias,
            'avisoDuplicado' => $avisoDuplicado,
        ]);
    }

    public function actionView($id)
    {
        $despensa = Despensa::obterAtivaPara(
            (int) Yii::$app->user->id
        );

        if ($despensa === null) {
            throw new NotFoundHttpException(
                'Despensa ativa não encontrada.'
            );
        }

        // Apenas consultar itens da despensa ativa
        $model = ItemDespensa::find()
            ->where([
                'id' => $id,
                'despensa_id' => $despensa->id,
            ])
            ->one();

        if ($model === null) {
            throw new NotFoundHttpException(
                'Item não encontrado nesta despensa.'
            );
        }

        $categoria = Categoria::findOne(
            $model->categoria_id
        );

        $movimentos = (new \yii\db\Query())
            ->from('{{%movimentos_stocks}}')
            ->where(['item_id' => $model->id])
            ->orderBy([
                'data_movimento' => SORT_DESC,
                'id' => SORT_DESC,
            ])
            ->all();

        return $this->render('view', [
            'model' => $model,
            'nomeCategoria' => $categoria !== null
                ? $categoria->nome
                : 'Sem categoria',
            'movimentos' => $movimentos,
        ]);

    }


    public function actionUpdate($id)
    {
        $despensa = Despensa::obterAtivaPara(
            (int) Yii::$app->user->id
        );

        if ($despensa === null) {
            throw new NotFoundHttpException(
                'Despensa ativa não encontrada.'
            );
        }

        // Procurar o item apenas na despensa ativa
        $model = ItemDespensa::find()
            ->where([
                'id' => $id,
                'despensa_id' => $despensa->id,
            ])
            ->one();

        if ($model === null) {
            throw new NotFoundHttpException(
                'Item não encontrado nesta despensa.'
            );
        }

        // Guardar a quantidade anterior
        $quantidadeAnterior = $model->quantidade;

        if ($model->load(Yii::$app->request->post())) {

            // Impedir alteração da despensa por POST
            $model->despensa_id = $despensa->id;

            if ($model->validate()) {

                $transaction = Yii::$app->db->beginTransaction();

                try {
                    if (!$model->save(false)) {
                        throw new \RuntimeException(
                            'Não foi possível atualizar o item.'
                        );
                    }

                    // Registar apenas alterações de quantidade
                    if (
                        (float) $quantidadeAnterior
                        != (float) $model->quantidade
                    ) {
                        Yii::$app->db->createCommand()->insert(
                            '{{%movimentos_stocks}}',
                            [
                                'item_id' => $model->id,
                                'utilizador_id' => Yii::$app->user->id,
                                'quantidade_anterior' => $quantidadeAnterior,
                                'quantidade_nova' => $model->quantidade,
                                'data_movimento' => new \yii\db\Expression('NOW()'),
                            ]
                        )->execute();
                    }

                    $transaction->commit();

                    Yii::$app->session->setFlash(
                        'success',
                        'Item atualizado com sucesso.'
                    );

                    return $this->redirect([
                        'view',
                        'id' => $model->id,
                    ]);

                } catch (\Throwable $e) {
                    $transaction->rollBack();
                    throw $e;
                }
            }
        }

        $categorias = \yii\helpers\ArrayHelper::map(
            Categoria::find()
                ->orderBy(['nome' => SORT_ASC])
                ->all(),
            'id',
            'nome'
        );

        return $this->render('update', [
            'model' => $model,
            'categorias' => $categorias,
        ]);
    }
}
