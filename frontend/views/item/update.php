<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\ItemDespensa $model */
/** @var array $categorias */

$this->title = 'Editar Item: ' . $model->nome;
?>

<div class="item-update container py-4">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'nome')
        ->textInput(['maxlength' => true]) ?>


    <?= $form->field($model, 'quantidade')
        ->input('number', [
            'min' => 0,
            'step' => 1,
        ]) ?>


    <?= $form->field($model, 'unidade')
        ->dropDownList([
            'un' => 'Unidade (un)',
            'kg' => 'Quilograma (kg)',
            'g' => 'Grama (g)',
            'L' => 'Litro (L)',
            'ml' => 'Mililitro (ml)',
            'pacote' => 'Pacote',
            'lata' => 'Lata',
        ], [
            'prompt' => 'Seleciona uma unidade',
        ]) ?>

    <?= $form->field($model, 'categoria_id')
        ->dropDownList($categorias, [
            'prompt' => 'Seleciona uma categoria',
        ]) ?>

    <?= $form->field($model, 'validade')
        ->input('date') ?>

    <div class="form-group mt-3">

        <?= Html::submitButton(
            'Guardar Alterações',
            ['class' => 'btn btn-primary']
        ) ?>

        <?= Html::a(
            'Cancelar',
            ['view', 'id' => $model->id],
            ['class' => 'btn btn-secondary ms-2']
        ) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>


<?php
$this->registerJs(<<<'JS'
    const unidade = document.getElementById('itemdespensa-unidade');
    const quantidade = document.getElementById('itemdespensa-quantidade');

    if (unidade && quantidade) {

        function atualizarIncremento() {
            const unidadesDecimais = ['kg', 'g', 'L', 'ml'];

            quantidade.step = unidadesDecimais.includes(unidade.value)
                ? '0.01'
                : '1';
        }

        unidade.addEventListener('change', atualizarIncremento);

        atualizarIncremento();
    }
JS);
?>