<?php

use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Utilizadores Eliminados';

?>

<div class="user-deleted">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(
            'Voltar à Gestão de Utilizadores',
            ['index'],
            ['class' => 'btn btn-secondary']
        ) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'username',
            'email:email',
            [
                'label' => 'Ações',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::beginForm(
                            ['restore', 'id' => $model->id],
                            'post'
                        )
                        . Html::submitButton(
                            'Restaurar',
                            [
                                'class' => 'btn btn-success btn-sm',
                                'data-confirm' =>
                                    'Tens a certeza que queres restaurar este utilizador?',
                            ]
                        )
                        . Html::endForm();
                },
            ],
        ],
    ]) ?>

</div>