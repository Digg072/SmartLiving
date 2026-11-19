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
        if (!Yii::$app->user->can('consultarItens')) {
            throw new \yii\web\ForbiddenHttpException(
                'Não tens permissão para consultar os itens da despensa.'
            );
        }

        $userId = (int) Yii::$app->user->id;

        $despensa = Despensa::obterAtivaPara($userId);

        $tipoDespensa = $despensa !== null
        && $despensa->agregado_id !== null
            ? 'Familiar'
            : 'Pessoal';


        $opcoesOrdenacao = [
            'categoria_az' => 'Categoria — A–Z',
            'categoria_za' => 'Categoria — Z–A',
            'validade_asc' => 'Validade — mais cedo',
            'validade_desc' => 'Validade — mais tarde',
            'nome_az' => 'Nome do item — A–Z',
            'nome_za' => 'Nome do item — Z–A',
        ];

        $ordenacao = Yii::$app->request->get('ordenar', 'categoria_az');

        if (!is_string($ordenacao) || !isset($opcoesOrdenacao[$ordenacao])) {
            $ordenacao = 'categoria_az';
        }

        $porCategoria = in_array($ordenacao, ['categoria_az', 'categoria_za'], true);
        $porValidade = in_array($ordenacao, ['validade_asc', 'validade_desc'], true);

        $itens = [];

        if ($despensa !== null) {
            $query = ItemDespensa::find()
                ->alias('i')
                ->select('i.*')
                ->where(['i.despensa_id' => $despensa->id]);

            if ($porCategoria) {
                $query->leftJoin(['c' => '{{%categorias}}'], 'c.id = i.categoria_id')
                    ->orderBy([
                        'c.nome' => $ordenacao === 'categoria_za' ? SORT_DESC : SORT_ASC,
                        'c.id' => SORT_ASC,
                        'i.nome' => SORT_ASC,
                    ]);
            } elseif ($porValidade) {
                $query->orderBy(new \yii\db\Expression('i.validade IS NULL'))
                    ->addOrderBy([
                        'i.validade' => $ordenacao === 'validade_desc' ? SORT_DESC : SORT_ASC,
                        'i.nome' => SORT_ASC,
                    ]);
            } else {
                $query->orderBy([
                    'i.nome' => $ordenacao === 'nome_za' ? SORT_DESC : SORT_ASC,
                ]);
            }

            $itens = $query->all();
        }

        $hoje = new \DateTimeImmutable('today', new \DateTimeZone('Europe/Lisbon'));

        // Categorias para apresentar os nomes dos itens
        $categorias = Categoria::find()
            ->indexBy('id')
            ->all();

        return $this->render('index', [
            'despensa' => $despensa,
            'tipoDespensa' => $tipoDespensa,
            'itens' => $itens,
            'categorias' => $categorias,
            'ordenacao' => $ordenacao,
            'opcoesOrdenacao' => $opcoesOrdenacao,
            'porCategoria' => $porCategoria,
            'hoje' => $hoje->format('Y-m-d'),
            'limiteAviso' => $hoje->modify('+3 days')->format('Y-m-d'),
        ]);
    }
}
