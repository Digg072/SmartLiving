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
        $userId = Yii::$app->user->id;
        $despensaPessoal = Despensa::garantirPessoal((int) $userId);

        // Verificar se o utilizador pertence a um agregado
        $agregadoId = (new Query())
            ->select('agregado_id')
            ->from('membros_agregado')
            ->where(['utilizador_id' => $userId])
            ->scalar();

        // Verificar também se é gestor de um agregado
        if ($agregadoId === false) {
            $agregadoId = (new Query())
                ->select('id')
                ->from('agregados_familiares')
                ->where(['gestor_id' => $userId])
                ->scalar();
        }

        // Selecionar a despensa ativa
        if ($agregadoId !== false) {
            $despensa = Despensa::find()
                ->where(['agregado_id' => $agregadoId])
                ->one();

            $tipoDespensa = 'Familiar';
        } else {
            $despensa = $despensaPessoal;
            $tipoDespensa = 'Pessoal';
        }

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
