<?php

use common\models\User;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\grid\SerialColumn;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

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
                            'attribute' => 'role_name',
                            'label' => 'Role',
                            'format' => 'raw',
                            'value' => function ($model) {
                                $roles = Yii::$app->authManager
                                        ->getRolesByUser($model->id);

                                $badges = [];

                                foreach ($roles as $role) {
                                    $badges[] = Html::tag(
                                            'span',
                                            Html::encode($role->name),
                                            ['class' => 'badge bg-primary me-1']
                                    );
                                }

                                return empty($badges)
                                        ? 'Sem role'
                                        : implode(' ', $badges);
                            },
                            'filter' => ArrayHelper::map(
                                    Yii::$app->authManager->getRoles(),
                                    'name',
                                    'name'
                            ),
                    ],
                    [
                            'attribute' => 'status',
                            'label' => 'Estado',
                            'value' => function ($model) {
                                return match ((int) $model->status) {
                                    10 => 'Ativo',
                                    9 => 'Inativo',
                                    0 => 'Eliminado',
                                    default => 'Desconhecido',
                                };
                            },
                            'filter' => [
                                    10 => 'Ativo',
                                    9 => 'Inativo',
                                    0 => 'Eliminado',
                            ],
                    ],
                    [
                            'class' => ActionColumn::class,
                            'visibleButtons' => [
                                    'view' => function ($model) {
                                        return Yii::$app->user->can('consultarUtilizadores');
                                    },
                                    'update' => function ($model) {
                                        return Yii::$app->user->can('editarUtilizador');
                                    },
                                    'delete' => function ($model) {
                                        return Yii::$app->user->can('eliminarUtilizador')
                                                && (int) $model->status !== 0;
                                    },
                            ],
                    ]
            ],
    ]) ?>

</div>
