<?php

use common\models\User;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var backend\models\UserForm $model */

?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'username')
            ->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')
            ->input('email') ?>

    <?= $form->field($model, 'password')
            ->passwordInput()
            ->hint(
                    $model->scenario === 'create'
                            ? 'Obrigatória na criação da conta.'
                            : 'Deixa vazio para manter a password atual.'
            ) ?>

    <?= $form->field($model, 'status')->dropDownList([
            User::STATUS_ACTIVE => 'Ativo',
            User::STATUS_INACTIVE => 'Inativo',
    ]) ?>

    <?= $form->field($model, 'role')->dropDownList([
            'utilizador' => 'Utilizador',
            'gestorAgregado' => 'Gestor de Agregado',
            'administrador' => 'Administrador',
    ]) ?>

    <div class="form-group mt-3">
        <?= Html::submitButton('Guardar', [
                'class' => 'btn btn-success',
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>