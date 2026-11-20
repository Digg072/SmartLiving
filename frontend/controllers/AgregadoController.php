<?php

namespace frontend\controllers;

use Yii;
use common\models\AgregadoFamiliar;
use yii\filters\AccessControl;

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
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index', [
            'agregado' => AgregadoFamiliar::doUtilizador((int) Yii::$app->user->id),
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
}
