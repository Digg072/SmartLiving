<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var $model \frontend\models\AdicionarMembroForm */
/** @var $agregado \common\models\AgregadoFamiliar */

$this->title = 'Adicionar Membro';
?>

<div class="container py-4">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        Agregado familiar:
        <strong><?= Html::encode($agregado->nome) ?></strong>
    </p>

    <p class="text-muted">
        Introduz o nome de utilizador de uma conta já registada
        no SmartLiving.
    </p>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'username')->textInput([
        'maxlength' => true,
        'placeholder' => 'Nome de utilizador',
    ]) ?>

    <div class="mt-3">

        <?= Html::submitButton('Adicionar membro', [
            'class' => 'btn btn-primary',
        ]) ?>

        <?= Html::a('Cancelar', ['index'], [
            'class' => 'btn btn-outline-secondary',
        ]) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>