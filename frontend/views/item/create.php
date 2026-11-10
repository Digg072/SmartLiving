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

    <?php if (!empty($avisoDuplicado)): ?>
        <div class="alert alert-warning mt-3">
            <?= Html::encode($avisoDuplicado) ?>
        </div>
    <?php endif; ?>

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
                        'step' => 1,
                ]) ?>


        <?= $form->field($model, 'unidade')->dropDownList([
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