<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var $model \common\models\AgregadoFamiliar */

$this->title = 'Editar agregado familiar';
?>

<div class="container py-4">

    <h1><?= Html::encode($this->title) ?></h1>

    <p class="text-muted">
        Altera o nome do teu agregado familiar.
    </p>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'nome')->textInput([
        'maxlength' => true,
    ]) ?>

    <div class="mt-3">

        <?= Html::submitButton('Guardar alterações', [
            'class' => 'btn btn-primary',
        ]) ?>

        <?= Html::a('Cancelar', ['index'], [
            'class' => 'btn btn-outline-secondary',
        ]) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>