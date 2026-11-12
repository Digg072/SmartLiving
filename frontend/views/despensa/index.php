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
        <p class="mt-3">
            <?= Html::a(
                    'Adicionar Item',
                    ['/item/create'],
                    ['class' => 'btn btn-primary']
            ) ?>
        </p>


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
                        <th class="text-end">Ações</th>
                    </tr>
                    </thead>

                    <tbody>

                    <?php foreach ($itens as $item): ?>

                        <tr>
                            <td>
                                <?= Html::a(
                                        Html::encode($item->nome),
                                        ['/item/view', 'id' => $item->id]
                                ) ?>
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

                            <td class="text-end text-nowrap">

                                <!-- Ver detalhes -->
                                <?= Html::a(
                                        '<i class="bi bi-eye"></i>',
                                        ['/item/view', 'id' => $item->id],
                                        [
                                                'class' => 'btn btn-outline-primary btn-sm',
                                                'title' => 'Ver detalhes',
                                                'aria-label' => 'Ver detalhes de ' . $item->nome,
                                        ]
                                ) ?>

                                <!-- Editar item -->
                                <?= Html::a(
                                        '<i class="bi bi-pencil-square"></i>',
                                        ['/item/update', 'id' => $item->id],
                                        [
                                                'class' => 'btn btn-outline-warning btn-sm',
                                                'title' => 'Editar item',
                                                'aria-label' => 'Editar ' . $item->nome,
                                        ]
                                ) ?>

                                <!-- Eliminar item -->
                                <?= Html::beginForm(
                                        ['/item/delete', 'id' => $item->id],
                                        'post',
                                        ['class' => 'd-inline']
                                ) ?>

                                <?= Html::submitButton(
                                        '<i class="bi bi-trash"></i>',
                                        [
                                                'class' => 'btn btn-outline-danger btn-sm',
                                                'title' => 'Eliminar item',
                                                'aria-label' => 'Eliminar ' . $item->nome,
                                                'data-confirm' =>
                                                        'Tens a certeza que queres eliminar este item? '
                                                        . 'O histórico de quantidades também será eliminado.',
                                        ]
                                ) ?>

                                <?= Html::endForm() ?>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php endif; ?>

    <?php endif; ?>

</div>
