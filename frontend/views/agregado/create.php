<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/** @var frontend\controllers\AgregadoController $model */

$this->title = 'Criar agregado familiar';
?>

    <h1><?= Html::encode($this->title) ?></h1>

<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'nome')->textInput(['maxlength' => true]) ?>

<?= Html::submitButton('Criar agregado', ['class' => 'btn btn-primary']) ?>
<?= Html::a('Cancelar', ['index'], ['class' => 'btn btn-outline-secondary']) ?>

<?php ActiveForm::end(); ?>