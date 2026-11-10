<?php

namespace frontend\controllers;

use Yii;
use common\models\Despensa;
use common\models\ItemDespensa;
use common\models\Categoria;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\web\Controller;

class DespensaController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['consultarDespensa'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {

        $userId = (int) Yii::$app->user->id;

        $despensa = Despensa::obterAtivaPara($userId);

        $tipoDespensa = $despensa !== null
        && $despensa->agregado_id !== null
            ? 'Familiar'
            : 'Pessoal';


        // Consultar os itens da despensa selecionada
        $itens = $despensa !== null
            ? ItemDespensa::find()
                ->where(['despensa_id' => $despensa->id])
                ->orderBy(['nome' => SORT_ASC])
                ->all()
            : [];

        // Categorias para apresentar os nomes dos itens
        $categorias = Categoria::find()
            ->indexBy('id')
            ->all();

        return $this->render('index', [
            'despensa' => $despensa,
            'tipoDespensa' => $tipoDespensa,
            'itens' => $itens,
            'categorias' => $categorias,
        ]);
    }
}
