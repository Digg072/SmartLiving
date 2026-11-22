<?php
use yii\helpers\Html;
/** @var frontend\controllers\AgregadoController $agregado */
/** @var frontend\controllers\AgregadoController $membros */

$this->title = 'Agregado Familiar';
?>

    <h1><?= Html::encode($this->title) ?></h1>

<?php if ($agregado === null): ?>
    <p>Ainda não pertences a um agregado familiar.</p>

    <?php if (Yii::$app->user->can('criarAgregado')): ?>
        <?= Html::a('Criar agregado', ['create'], ['class' => 'btn btn-primary']) ?>
    <?php endif; ?>
<?php else: ?>
    <h2><?= Html::encode($agregado->nome) ?></h2>
    <p>A despensa deste agregado é partilhada pelos seus membros.</p>

    <?= Html::a('Abrir despensa partilhada', ['/despensa/index'], [
            'class' => 'btn btn-primary',
    ]) ?>

    <hr>

    <h3>Membros do agregado</h3>

    <ul class="list-group mb-4">

        <?php foreach ($membros as $membro): ?>

            <li class="list-group-item">

                <?= Html::encode($membro->username) ?>

                <?php if ((int) $membro->id === (int) $agregado->gestor_id): ?>
                    <span class="badge bg-primary ms-2">
                    Gestor
                </span>
                <?php endif; ?>

            </li>

        <?php endforeach; ?>

    </ul>

    <?php if (
            (int) $agregado->gestor_id === (int) Yii::$app->user->id &&
            Yii::$app->user->can('adicionarMembroAgregado')
    ): ?>

        <?= Html::a(
                'Adicionar membro',
                ['add-member'],
                ['class' => 'btn btn-primary']
        ) ?>

    <?php endif; ?>
<?php endif; ?>