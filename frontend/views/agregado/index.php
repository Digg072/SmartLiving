<?php
use yii\helpers\Html;
/** @var frontend\controllers\AgregadoController $agregado */

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
<?php endif; ?>