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
                        'actions' => ['create'],
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

        if ($model->load(Yii::$app->request->post())) {

            // Nunca confiar no ID enviado pelo formulário
            $model->despensa_id = $despensa->id;

            if ($model->save()) {
                Yii::$app->session->setFlash(
                    'success',
                    'Item adicionado com sucesso.'
                );

                return $this->redirect(['/despensa/index']);
            }
        }

        $categorias = ArrayHelper::map(
            Categoria::find()
                ->orderBy(['nome' => SORT_ASC])
                ->all(),
            'id',
            'nome'
        );

        return $this->render('create', [
            'model' => $model,
            'categorias' => $categorias,
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

        return $this->render('view', [
            'model' => $model,
            'nomeCategoria' => $categoria !== null
                ? $categoria->nome
                : 'Sem categoria',
        ]);
    }
}
