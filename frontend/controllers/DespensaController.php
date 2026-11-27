<?php

namespace frontend\controllers;

use Yii;
use common\models\Despensa;
use common\models\ItemDespensa;
use common\models\Categoria;
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

        $ordenacao = Yii::$app->request->get(
            'ordenar',
            'categoria_az'
        );

        if (
            !is_string($ordenacao) ||
            !isset($opcoesOrdenacao[$ordenacao])
        ) {
            $ordenacao = 'categoria_az';
        }

        $porCategoria = in_array(
            $ordenacao,
            ['categoria_az', 'categoria_za'],
            true
        );

        $porValidade = in_array(
            $ordenacao,
            ['validade_asc', 'validade_desc'],
            true
        );

        $categorias = Categoria::find()
            ->indexBy('id')
            ->all();

        $itens = [];

        if ($despensa !== null) {

            $query = ItemDespensa::find()
                ->where(['despensa_id' => $despensa->id]);

            // Ordenação por validade.
            if ($porValidade) {
                $query
                    ->orderBy(
                        new \yii\db\Expression('validade IS NULL')
                    )
                    ->addOrderBy([
                        'validade' => $ordenacao === 'validade_desc'
                            ? SORT_DESC
                            : SORT_ASC,
                        'nome' => SORT_ASC,
                    ]);
            }

            // Ordenação por nome.
            elseif (!$porCategoria) {
                $query->orderBy([
                    'nome' => $ordenacao === 'nome_za'
                        ? SORT_DESC
                        : SORT_ASC,
                ]);
            }

            $itens = $query->all();

            // Ordenação por nome da categoria, sem JOIN.
            if ($porCategoria) {

                usort($itens, function ($a, $b) use (
                    $categorias,
                    $ordenacao
                ) {
                    $nomeCategoriaA =
                        $categorias[(int) $a->categoria_id]->nome
                        ?? 'Sem categoria';

                    $nomeCategoriaB =
                        $categorias[(int) $b->categoria_id]->nome
                        ?? 'Sem categoria';

                    $comparacao = strcasecmp(
                        $nomeCategoriaA,
                        $nomeCategoriaB
                    );

                    if ($ordenacao === 'categoria_za') {
                        $comparacao = -$comparacao;
                    }

                    if ($comparacao !== 0) {
                        return $comparacao;
                    }

                    // Manter os produtos da mesma categoria juntos.
                    $comparacao = (int) $a->categoria_id
                        <=> (int) $b->categoria_id;

                    if ($comparacao !== 0) {
                        return $comparacao;
                    }

                    // Dentro da categoria, ordenar pelo nome do item.
                    return strcasecmp($a->nome, $b->nome);
                });
            }
        }

        $hoje = new \DateTimeImmutable(
            'today',
            new \DateTimeZone('Europe/Lisbon')
        );

        return $this->render('index', [
            'despensa' => $despensa,
            'tipoDespensa' => $tipoDespensa,
            'itens' => $itens,
            'categorias' => $categorias,
            'ordenacao' => $ordenacao,
            'opcoesOrdenacao' => $opcoesOrdenacao,
            'porCategoria' => $porCategoria,
            'hoje' => $hoje->format('Y-m-d'),
            'limiteAviso' => $hoje
                ->modify('+3 days')
                ->format('Y-m-d'),
        ]);
    }
}
