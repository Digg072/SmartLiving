<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var common\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

$this->title = 'Iniciar sessão';
$this->params['meta_description'] = 'Inicia sessão na tua conta SmartLiving.';
?>

<div class="site-login sl-auth d-flex justify-content-center align-items-center py-5">
    <div class="sl-auth-card">
        <div class="text-center mb-4">
            <h1 class="h4 fw-bold mb-2"><?= Html::encode($this->title) ?></h1>
            <p class="sl-auth-subtitle mb-0">Acede à tua despensa no SmartLiving.</p>
        </div>

        <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

        <?= $form->field($model, 'username')
                ->textInput([
                        'autofocus' => true,
                        'autocomplete' => 'username',
                        'placeholder' => 'Nome de utilizador',
                ])
                ->label('Nome de utilizador') ?>

        <?= $form->field($model, 'password')
                ->passwordInput([
                        'autocomplete' => 'current-password',
                        'placeholder' => 'Palavra-passe',
                ])
                ->label('Palavra-passe') ?>

        <div class="sl-auth-remember">
            <?= $form->field($model, 'rememberMe')
                    ->checkbox(['label' => 'Manter sessão iniciada']) ?>
        </div>

        <?= Html::submitButton('Iniciar sessão', [
                'class' => 'btn sl-auth-button w-100',
                'name' => 'login-button',
        ]) ?>

        <?php ActiveForm::end(); ?>

        <div class="sl-auth-links text-center mt-3">
            <?= Html::a('Esqueceste-te da palavra-passe?', ['/site/request-password-reset']) ?>
            <span class="sl-auth-separator" aria-hidden="true">·</span>
            <?= Html::a('Reenviar email de verificação', ['/site/resend-verification-email']) ?>
        </div>

        <div class="sl-auth-footer text-center mt-4">
            Ainda não tens conta?
            <?= Html::a('Regista-te aqui', ['/site/signup'], ['class' => 'fw-semibold']) ?>
        </div>
    </div>
</div>
