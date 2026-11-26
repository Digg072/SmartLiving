<?php

use yii\helpers\Html;

/** @var $agregado \common\models\AgregadoFamiliar|null */
/** @var $membros \common\models\User[] */
/** @var $historico array */

$this->title = 'Meu Agregado Familiar';

?>

<div class="container py-4">

    <h1 class="h3 fw-bold mb-4">
        <?= Html::encode($this->title) ?>
    </h1>

    <?php if ($agregado === null): ?>

        <div class="card shadow-sm">
            <div class="card-body">
                <p>Ainda não pertences a um agregado familiar.</p>

                <?php if (Yii::$app->user->can('criarAgregado')): ?>
                    <?= Html::a('Criar agregado familiar', ['create'], [
                            'class' => 'btn btn-primary btn-sm'
                    ]) ?>
                <?php endif; ?>
            </div>
        </div>

    <?php else: ?>

        <?php
        $utilizadorAtual = (int) Yii::$app->user->id;
        $eGestor = (int) $agregado->gestor_id === $utilizadorAtual;
        ?>

        <!-- CABEÇALHO DO AGREGADO -->

        <div class="card shadow-sm mb-4">
            <div class="card-body">

                <div class="d-flex justify-content-between
                            align-items-center flex-wrap gap-3">

                    <div class="d-flex align-items-center gap-3">

                        <i class="bi bi-people-fill fs-2 text-secondary"></i>

                        <div>
                            <h2 class="h5 fw-bold mb-1">
                                <?= Html::encode($agregado->nome) ?>
                            </h2>

                            <span class="text-muted small">
                                <?= count($membros) ?>
                                <?= count($membros) === 1 ? 'membro' : 'membros' ?>
                            </span>
                        </div>

                    </div>

                    <div class="d-flex flex-wrap gap-2">

                        <?php if ($eGestor &&
                                Yii::$app->user->can('editarAgregado')): ?>

                            <?= Html::a(
                                    '<i class="bi bi-pencil"></i> Editar nome',
                                    ['update'],
                                    ['class' => 'btn btn-outline-secondary btn-sm']
                            ) ?>

                        <?php endif; ?>

                        <?= Html::a(
                                '<i class="bi bi-box-seam"></i> Abrir despensa',
                                ['/despensa/index'],
                                ['class' => 'btn btn-primary btn-sm']
                        ) ?>

                    </div>

                </div>

            </div>
        </div>


        <!-- MEMBROS DO AGREGADO -->

        <div class="d-flex justify-content-between
                    align-items-center flex-wrap gap-2 mb-3">

            <h3 class="h5 fw-bold mb-0">
                Membros do agregado
            </h3>

            <?php if ($eGestor &&
                    Yii::$app->user->can('adicionarMembroAgregado')): ?>

                <?= Html::a(
                        '<i class="bi bi-person-plus"></i> Adicionar membro',
                        ['add-member'],
                        ['class' => 'btn btn-outline-primary btn-sm']
                ) ?>

            <?php endif; ?>

        </div>

        <div class="table-responsive mb-3">

            <table class="table table-bordered table-hover align-middle">

                <thead class="table-light">
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Função</th>
                    <th class="text-center" style="width: 100px">
                        Ações
                    </th>
                </tr>
                </thead>

                <tbody>

                <?php foreach ($membros as $membro): ?>

                    <?php
                    $membroEGestor =
                            (int) $membro->id === (int) $agregado->gestor_id;
                    ?>

                    <tr>

                        <td>
                            <?= Html::encode($membro->username) ?>
                        </td>

                        <td>
                            <?= Html::encode($membro->email) ?>
                        </td>

                        <td>
                            <?php if ($membroEGestor): ?>
                                <span class="badge bg-primary">
                                    Gestor
                                </span>
                            <?php else: ?>
                                <span class="badge bg-secondary">
                                    Membro
                                </span>
                            <?php endif; ?>
                        </td>

                        <td class="text-center">

                            <?php if (
                                    $eGestor &&
                                    !$membroEGestor &&
                                    Yii::$app->user->can('removerMembroAgregado')
                            ): ?>

                                <?= Html::beginForm(
                                        ['remove-member', 'id' => $membro->id],
                                        'post',
                                        [
                                                'class' => 'd-inline',
                                                'onsubmit' =>
                                                        "return confirm('Remover este membro do agregado?');"
                                        ]
                                ) ?>

                                <?= Html::submitButton(
                                        '<i class="bi bi-trash"></i>',
                                        [
                                                'class' => 'btn btn-outline-danger btn-sm',
                                                'title' => 'Remover membro',
                                                'aria-label' => 'Remover membro',
                                        ]
                                ) ?>

                                <?= Html::endForm() ?>

                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>

                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        </div>


        <!-- SAIR DO AGREGADO (MEMBRO NORMAL) -->

        <?php if (!$eGestor &&
                Yii::$app->user->can('sairAgregado')): ?>

            <div class="d-flex justify-content-end mb-4">

                <?= Html::beginForm(
                        ['leave'],
                        'post',
                        [
                                'onsubmit' =>
                                        "return confirm('Tens a certeza de que queres sair do agregado?');"
                        ]
                ) ?>

                <?= Html::submitButton(
                        '<i class="bi bi-box-arrow-right"></i> Sair do agregado',
                        ['class' => 'btn btn-outline-danger btn-sm']
                ) ?>

                <?= Html::endForm() ?>

            </div>

        <?php endif; ?>


        <!-- ÚLTIMAS ALTERAÇÕES -->

        <h3 class="h5 fw-bold mb-3 mt-4">
            Últimas alterações
        </h3>

        <div class="table-responsive mb-4">

            <table class="table table-bordered table-hover align-middle">

                <thead class="table-light">
                <tr>
                    <th>Nome</th>
                    <th>Alteração feita</th>
                    <th style="width: 170px">Data</th>
                </tr>
                </thead>

                <tbody>

                <?php if (empty($historico)): ?>

                    <tr>
                        <td colspan="3" class="text-center text-muted py-3">
                            Ainda não existem alterações registadas.
                        </td>
                    </tr>

                <?php else: ?>

                    <?php foreach ($historico as $movimento): ?>

                        <?php
                        $anterior = (float) $movimento['quantidade_anterior'];
                        $nova = (float) $movimento['quantidade_nova'];

                        $diferenca = round($nova - $anterior, 2);

                        $quantidade = number_format(
                                abs($diferenca), 2, ',', '.'
                        );

                        $quantidade = rtrim(
                                rtrim($quantidade, '0'), ','
                        );

                        $nomeItem = $movimento['nome_item'];
                        $nomeUtilizador = $movimento['nome_utilizador'];
                        $unidade = trim((string) $movimento['unidade']);

                        $quantidadeTexto = $quantidade;

                        if ($unidade !== '' && $unidade !== 'un') {
                            $quantidadeTexto .= ' ' . $unidade . ' de';
                        }

                        $ePrimeiroMovimento =
                                (int) $movimento['id_movimento'] ===
                                (int) $movimento['primeiro_movimento_id'];

                        if (
                                $ePrimeiroMovimento &&
                                $anterior == 0 &&
                                $nova > 0
                        ) {
                            $descricao = $nomeUtilizador .
                                    ' criou o item ' .
                                    $nomeItem .
                                    ' (' . $quantidadeTexto . ')';

                        } elseif ($diferenca > 0) {
                            $descricao = $nomeUtilizador .
                                    ' adicionou ' .
                                    $quantidadeTexto . ' ' . $nomeItem;

                        } elseif ($diferenca < 0) {
                            $descricao = $nomeUtilizador .
                                    ' retirou ' .
                                    $quantidadeTexto . ' ' . $nomeItem;

                        } else {
                            $descricao = $nomeUtilizador .
                                    ' ajustou a quantidade de ' . $nomeItem;
                        }
                        ?>

                        <tr>

                            <td>
                                <?= Html::encode($nomeUtilizador) ?>
                            </td>

                            <td>
                                <?= Html::encode($descricao) ?>
                            </td>

                            <td>
                                <?= Yii::$app->formatter->asDatetime(
                                        $movimento['data_movimento'],
                                        'php:d/m/Y H:i'
                                ) ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

                </tbody>

            </table>

        </div>


        <!-- ELIMINAR AGREGADO (GESTOR) -->

        <?php if ($eGestor &&
                Yii::$app->user->can('eliminarAgregado')): ?>

            <div class="border border-danger rounded p-3 mt-4">

                <div class="d-flex justify-content-between
                            align-items-center flex-wrap gap-3">

                    <div>

                        <h4 class="h6 text-danger fw-bold mb-2">
                            <i class="bi bi-trash"></i>
                            Eliminar agregado familiar
                        </h4>

                        <p class="text-muted small mb-0">
                            Esta ação é permanente. A despensa familiar,
                            os seus itens e o histórico serão eliminados.
                            As despensas pessoais serão preservadas.
                        </p>

                    </div>

                    <?= Html::beginForm(
                            ['delete'],
                            'post',
                            [
                                    'onsubmit' =>
                                            "return confirm('Eliminar permanentemente o agregado e todos os seus itens familiares?');"
                            ]
                    ) ?>

                    <?= Html::submitButton(
                            'Eliminar agregado',
                            ['class' => 'btn btn-danger btn-sm']
                    ) ?>

                    <?= Html::endForm() ?>

                </div>

            </div>

        <?php endif; ?>

    <?php endif; ?>

</div>