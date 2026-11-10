<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\ItemDespensa $model */
/** @var array $categorias */

$this->title = 'Adicionar Item';
?>

<div class="item-create container py-4">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (empty($categorias)): ?>

        <div class="alert alert-info mt-3">
            Ainda não existem categorias disponíveis.
        </div>

        <?= Html::a(
            'Voltar à Despensa',
            ['/despensa/index'],
            ['class' => 'btn btn-secondary']
        ) ?>

    <?php else: ?>

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'nome')
            ->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'quantidade')
            ->input('number', [
                'min' => 0,
                'step' => '0.01',
            ]) ?>

        <?= $form->field($model, 'unidade')
            ->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'categoria_id')
            ->dropDownList($categorias, [
                'prompt' => 'Seleciona uma categoria',
            ]) ?>

        <?= $form->field($model, 'validade')
            ->input('date') ?>

        <div class="form-group mt-3">

            <?= Html::submitButton(
                'Guardar Item',
                ['class' => 'btn btn-primary']
            ) ?>

            <?= Html::a(
                'Cancelar',
                ['/despensa/index'],
                ['class' => 'btn btn-secondary ms-2']
            ) ?>

        </div>

        <?php ActiveForm::end(); ?>

    <?php endif; ?>

</div>
