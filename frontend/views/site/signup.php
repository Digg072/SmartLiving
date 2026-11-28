<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var frontend\models\SignupForm $model */

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

$this->title = 'Criar conta';
$this->params['meta_description'] = 'Cria uma conta SmartLiving para gerir a tua despensa.';
?>

<div class="site-signup sl-auth d-flex justify-content-center align-items-center py-5">
    <div class="sl-auth-card">
        <div class="text-center mb-4">
            <h1 class="h4 fw-bold mb-2"><?= Html::encode($this->title) ?></h1>
            <p class="sl-auth-subtitle mb-0">Regista-te para começares a organizar a tua despensa.</p>
        </div>

        <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>

        <?= $form->field($model, 'nome')
                ->textInput([
                        'autofocus' => true,
                        'maxlength' => true,
                        'autocomplete' => 'name',
                        'placeholder' => 'O teu nome completo',
                ])
                ->label('Nome de Utilizador') ?>

        <?= $form->field($model, 'username')
                ->textInput([
                        'autocomplete' => 'username',
                        'placeholder' => 'Nome de utilizador',
                ])
                ->label('Nome de utilizador') ?>

        <?= $form->field($model, 'email')
                ->input('email', [
                        'autocomplete' => 'email',
                        'placeholder' => 'exemplo@email.com',
                ])
                ->label('Email') ?>

        <?= $form->field($model, 'password')
                ->passwordInput([
                        'autocomplete' => 'new-password',
                        'placeholder' => 'Palavra-passe',
                ])
                ->label('Palavra-passe') ?>

        <?= Html::submitButton('Criar conta', [
                'class' => 'btn sl-auth-button w-100 mt-2',
                'name' => 'signup-button',
        ]) ?>

        <?php ActiveForm::end(); ?>

        <div class="sl-auth-footer text-center mt-4">
            Já tens conta?
            <?= Html::a('Inicia sessão aqui', ['/site/login'], ['class' => 'fw-semibold']) ?>
        </div>
    </div>
</div>