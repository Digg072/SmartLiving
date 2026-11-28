<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\AlterarPasswordForm $model */

$this->title = 'Alterar Password';
?>

<div class="container py-5">

    <div class="card shadow-sm border-0 rounded-4 mx-auto"
         style="max-width: 650px;">

        <div class="card-body p-4 p-md-5">

            <h1 class="h3 fw-bold mb-2">
                <?= Html::encode($this->title) ?>
            </h1>

            <p class="text-muted mb-4">
                Atualiza a password da tua conta SmartLiving.
            </p>

            <?php $form = ActiveForm::begin([
                'id' => 'alterar-password-form',
            ]); ?>

            <?= $form->field($model, 'passwordAtual')
                ->passwordInput([
                    'autocomplete' => 'current-password',
                ]) ?>

            <?= $form->field($model, 'passwordNova')
                ->passwordInput([
                    'autocomplete' => 'new-password',
                ]) ?>

            <?= $form->field($model, 'passwordConfirmacao')
                ->passwordInput([
                    'autocomplete' => 'new-password',
                ]) ?>

            <div class="d-flex gap-2 mt-4">

                <?= Html::a('Cancelar', ['/perfil/index'], [
                    'class' => 'btn btn-outline-secondary',
                ]) ?>

                <?= Html::submitButton('Guardar Password', [
                    'class' => 'btn text-white',
                    'style' => 'background-color: #f87f02;',
                ]) ?>

            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>

</div>
