<?php

namespace frontend\controllers;

use Yii;
use common\models\AgregadoFamiliar;
use yii\filters\AccessControl;
use frontend\models\AdicionarMembroForm;
use common\models\MembroAgregado;
use common\models\User;
use yii\web\ForbiddenHttpException;

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
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $agregado = AgregadoFamiliar::doUtilizador(
            (int) Yii::$app->user->id
        );

        $membros = [];

        if ($agregado !== null) {
            $idsMembros = MembroAgregado::find()
                ->select('utilizador_id')
                ->where(['agregado_id' => $agregado->id]);

            $membros = User::find()
                ->where(['id' => $idsMembros])
                ->orderBy(['username' => SORT_ASC])
                ->all();
        }

        return $this->render('index', [
            'agregado' => $agregado,
            'membros' => $membros,
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
}
