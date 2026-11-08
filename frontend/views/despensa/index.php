<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Despensa|null $despensa */
/** @var string $tipoDespensa */
/** @var common\models\ItemDespensa[] $itens */
/** @var common\models\Categoria[] $categorias */

$this->title = 'A Minha Despensa';

?>

<div class="despensa-index container py-4">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if ($despensa === null): ?>

        <div class="alert alert-info mt-4">
            Ainda não existe uma despensa associada
            à tua conta.
        </div>

    <?php else: ?>

        <div class="card my-4">
            <div class="card-body">

                <h4>
                    <?= Html::encode($despensa->nome) ?>
                </h4>

                <p class="mb-0">
                    Tipo de despensa:
                    <strong>
                        <?= Html::encode($tipoDespensa) ?>
                    </strong>
                </p>

            </div>
        </div>

        <h3>Itens da Despensa</h3>

        <?php if (empty($itens)): ?>

            <div class="alert alert-info">
                A tua despensa ainda não contém itens.
            </div>

        <?php else: ?>

            <div class="table-responsive">

                <table class="table table-striped table-hover">

                    <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Quantidade</th>
                        <th>Unidade</th>
                        <th>Categoria</th>
                        <th>Validade</th>
                    </tr>
                    </thead>

                    <tbody>

                    <?php foreach ($itens as $item): ?>

                        <tr>
                            <td>
                                <?= Html::encode($item->nome) ?>
                            </td>

                            <td>
                                <?= Html::encode($item->quantidade) ?>
                            </td>

                            <td>
                                <?= Html::encode($item->unidade) ?>
                            </td>

                            <td>
                                <?= isset($categorias[$item->categoria_id])
                                    ? Html::encode(
                                        $categorias[$item->categoria_id]->nome
                                    )
                                    : '—' ?>
                            </td>

                            <td>
                                <?= $item->validade !== null
                                    ? Html::encode($item->validade)
                                    : 'Sem validade' ?>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php endif; ?>

    <?php endif; ?>

</div>
