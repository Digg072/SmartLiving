<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\PerfilForm $model */

$this->title = 'Meu Perfil';

$this->registerCss('
    .sl-profile-card {
        max-width: 850px;
        margin: 0 auto;
        border: 1px solid #e8e8e8;
        border-radius: 16px;
    }

    .sl-profile-card .form-control:focus {
        border-color: #f87f02;
        box-shadow: 0 0 0 0.2rem rgba(248, 127, 2, 0.15);
    }

    .sl-profile-save {
        background-color: #f87f02;
        border-color: #f87f02;
        color: #fff;
    }

    .sl-profile-save:hover {
        background-color: #df7100;
        border-color: #df7100;
        color: #fff;
    }
');
?>

<div class="container py-5">

    <div class="sl-profile-card bg-white p-4 p-md-5 shadow-sm">

        <h1 class="h3 fw-bold mb-2">
            <?= Html::encode($this->title) ?>
        </h1>

        <p class="text-muted mb-4">
            Consulta e atualiza os dados da tua conta SmartLiving.
        </p>

        <h2 class="h5 fw-semibold mb-4">
            Dados Pessoais
        </h2>

        <?php $form = ActiveForm::begin([
            'id' => 'perfil-form',
        ]); ?>

        <?= $form->field($model, 'nome')
            ->textInput([
                'maxlength' => true,
                'autocomplete' => 'name',
            ]) ?>

        <?= $form->field($model, 'username')
            ->textInput([
                'maxlength' => true,
                'autocomplete' => 'username',
            ]) ?>

        <?= $form->field($model, 'email')
            ->input('email', [
                'maxlength' => true,
                'autocomplete' => 'email',
            ]) ?>

        <?= $form->field($model, 'telefone')
            ->input('tel', [
                'maxlength' => true,
                'autocomplete' => 'tel',
            ]) ?>

        <div class="d-flex flex-wrap gap-2 mt-4">

            <?= Html::a('Cancelar', ['/site/index'], [
                'class' => 'btn btn-outline-secondary',
            ]) ?>

            <?= Html::submitButton('Guardar', [
                'class' => 'btn sl-profile-save',
            ]) ?>

            <?= Html::a('Alterar Password', ['/perfil/password'], [
                'class' => 'btn btn-outline-dark',
            ]) ?>

        </div>

        <?php ActiveForm::end(); ?>

        <div class="mt-3">
            <?= Html::beginForm(['/perfil/delete'], 'post', [
                'onsubmit' => "return confirm('Tens a certeza de que pretendes eliminar a tua conta?');",
            ]) ?>

            <?= Html::submitButton('Eliminar Conta', [
                'class' => 'btn btn-outline-danger',
            ]) ?>

            <?= Html::endForm() ?>
        </div>

    </div>

</div>