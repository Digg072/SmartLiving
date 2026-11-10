<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\ItemDespensa $model */
/** @var string $nomeCategoria */

$this->title = $model->nome;
?>

<div class="item-view container py-4">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'nome',
            'quantidade',
            'unidade',
            [
                'label' => 'Categoria',
                'value' => $nomeCategoria,
            ],
            [
                'attribute' => 'validade',
                'value' => $model->validade
                    ?: 'Sem validade',
            ],
        ],
    ]) ?>

    <?= Html::a(
        'Voltar à Despensa',
        ['/despensa/index'],
        ['class' => 'btn btn-secondary']
    ) ?>

</div>
