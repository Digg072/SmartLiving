<?php

use common\models\User;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\UserSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Gestão de Utilizadores';

?>

<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Criar Utilizador', ['create'], [
                'class' => 'btn btn-success',
        ]) ?>

        <?= Html::a(
                'Utilizadores Eliminados',
                ['deleted'],
                ['class' => 'btn btn-outline-secondary ms-2']
        ) ?>
    </p>

    <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                    ['class' => SerialColumn::class],

                    'username',
                    'email:email',

                    [
                            'label' => 'Cargos',
                            'format' => 'raw',
                            'value' => function ($model) {
                                $roles = Yii::$app->authManager
                                        ->getRolesByUser($model->id);

                                $badges = array_map(function ($role) {
                                    return \yii\helpers\Html::tag(
                                            'span',
                                            \yii\helpers\Html::encode($role->name),
                                            ['class' => 'badge bg-primary me-1']
                                    );
                                }, $roles);

                                return $badges
                                        ? implode(' ', $badges)
                                        : 'Sem cargos';
                            },
                    ],
                    [
                            'attribute' => 'status',
                            'value' => function ($model) {
                                return $model->status === User::STATUS_ACTIVE
                                        ? 'Ativo'
                                        : 'Inativo';
                            },
                            'filter' => [
                                    User::STATUS_ACTIVE => 'Ativo',
                                    User::STATUS_INACTIVE => 'Inativo',
                            ],
                    ],


                    ['class' => ActionColumn::class],
            ],
    ]) ?>

</div>
