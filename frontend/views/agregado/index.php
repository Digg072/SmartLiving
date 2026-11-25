<?php
use yii\helpers\Html;
/** @var frontend\controllers\AgregadoController $agregado */
/** @var frontend\controllers\AgregadoController $membros */

$this->title = 'Agregado Familiar';
?>
<?php if ($agregado === null): ?>
    <p>Ainda não pertences a um agregado familiar.</p>

    <?php if (Yii::$app->user->can('criarAgregado')): ?>
        <?= Html::a('Criar agregado', ['create'], ['class' => 'btn btn-primary']) ?>
    <?php endif; ?>
<?php else: ?>
    <h1><?= Html::encode($agregado->nome) ?></h1>

    <?php if (
            (int) $agregado->gestor_id === (int) Yii::$app->user->id &&
            Yii::$app->user->can('editarAgregado')
    ): ?>

        <?= Html::a(
                'Editar nome',
                ['update'],
                ['class' => 'btn btn-outline-primary mb-3']
        ) ?>

    <?php endif; ?>

    <p>A despensa deste agregado é partilhada pelos seus membros.</p>

    <?= Html::a('Abrir despensa partilhada', ['/despensa/index'], [
            'class' => 'btn btn-primary',
    ]) ?>

    <hr>

    <h3>Membros do agregado</h3>

    <?php
    $utilizadorAtual = (int) Yii::$app->user->id;

    $eGestor = $utilizadorAtual === (int) $agregado->gestor_id;
    ?>

    <ul class="list-group mb-4">

        <?php foreach ($membros as $membro): ?>

            <li class="list-group-item d-flex justify-content-between align-items-center">

                <div>
                    <?= Html::encode($membro->username) ?>

                    <?php if ((int) $membro->id === (int) $agregado->gestor_id): ?>
                        <span class="badge bg-primary ms-2">
                        Gestor
                    </span>
                    <?php endif; ?>
                </div>

                <?php if (
                        $eGestor &&
                        (int) $membro->id !== $utilizadorAtual &&
                        Yii::$app->user->can('removerMembroAgregado')
                ): ?>

                    <?= Html::beginForm(
                            ['remove-member', 'id' => $membro->id],
                            'post',
                            [
                                    'class' => 'd-inline',
                                    'onsubmit' => "return confirm('Tens a certeza de que queres remover este membro?');",
                            ]
                    ) ?>

                    <?= Html::submitButton('Remover', [
                            'class' => 'btn btn-sm btn-outline-danger',
                    ]) ?>

                    <?= Html::endForm() ?>

                <?php endif; ?>

            </li>

        <?php endforeach; ?>

    </ul>

    <?php if ($eGestor && Yii::$app->user->can('adicionarMembroAgregado')): ?>

        <?= Html::a(
                'Adicionar membro',
                ['add-member'],
                ['class' => 'btn btn-primary']
        ) ?>

    <?php elseif (!$eGestor && Yii::$app->user->can('sairAgregado')): ?>

        <?= Html::beginForm(
                ['leave'],
                'post',
                [
                        'onsubmit' => "return confirm('Tens a certeza de que queres sair do agregado familiar?');",
                ]
        ) ?>

        <?= Html::submitButton('Sair do agregado', [
                'class' => 'btn btn-outline-danger',
        ]) ?>

        <?= Html::endForm() ?>

    <?php endif; ?>

    <?php if (
            $eGestor &&
            Yii::$app->user->can('eliminarAgregado')
    ): ?>

        <hr class="my-4">

        <h4 class="text-danger">Eliminar agregado familiar</h4>

        <p class="text-muted">
            Esta ação é permanente. Todos os membros serão removidos
            do agregado e a despensa familiar, incluindo os seus itens
            e histórico, será eliminada.
            As despensas pessoais serão preservadas.
        </p>

        <?= Html::beginForm(
                ['delete'],
                'post',
                [
                        'onsubmit' =>
                                "return confirm('Tens a certeza de que queres eliminar o agregado familiar? Esta ação é irreversível e apagará todos os itens e movimentos da despensa familiar.');",
                ]
        ) ?>

        <?= Html::submitButton('Eliminar agregado', [
                'class' => 'btn btn-danger',
        ]) ?>

        <?= Html::endForm() ?>

    <?php endif; ?>
<?php endif; ?>