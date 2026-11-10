<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\ItemDespensa $model */
/** @var string $nomeCategoria */
/** @var array $movimentos */

$this->title = $model->nome;
?>

<div class="item-view container py-4">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'nome',
            'quantidade',
            'unidade',
            [
                'label' => 'Categoria',
                'value' => $nomeCategoria,
            ],
            [
                'attribute' => 'validade',
                'value' => $model->validade
                    ?: 'Sem validade',
            ],
        ],
    ]) ?>

    <h3 class="mt-4">Histórico de Quantidades</h3>
    <?php if (empty($movimentos)): ?>

        <div class="alert alert-info">
            Ainda não existem alterações de quantidade.
        </div>
    <?php else: ?>

        <div class="table-responsive">

            <table class="table table-striped">

                <thead>
                <tr>
                    <th>Data</th>
                    <th>Quantidade Anterior</th>
                    <th>Quantidade Nova</th>
                </tr>
                </thead>

                <tbody>

                <?php foreach ($movimentos as $movimento): ?>

                    <tr>
                        <td>
                            <?= Html::encode(
                                    $movimento['data_movimento']
                            ) ?>
                        </td>

                        <td>
                            <?= Html::encode(
                                    $movimento['quantidade_anterior']
                            ) ?>
                        </td>

                        <td>
                            <?= Html::encode(
                                    $movimento['quantidade_nova']
                            ) ?>
                        </td>
                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php endif; ?>

    <?= Html::a(
            'Editar Item',
            ['update', 'id' => $model->id],
            ['class' => 'btn btn-primary me-2']
    ) ?>

    <?= Html::a(
        'Voltar à Despensa',
        ['/despensa/index'],
        ['class' => 'btn btn-secondary']
    ) ?>

</div>
