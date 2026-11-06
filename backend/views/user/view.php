<?php

use common\models\User;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\User $model */

$this->title = $model->username;

?>

<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Editar', [
                'update',
                'id' => $model->id,
        ], [
                'class' => 'btn btn-primary',
        ]) ?>

        <?= Html::beginForm([
                'delete',
                'id' => $model->id,
        ], 'post', [
                'class' => 'd-inline',
        ]) ?>

        <?= Html::submitButton('Eliminar', [
                'class' => 'btn btn-danger',
                'data-confirm' => 'Eliminar este utilizador?',
        ]) ?>

        <?= Html::endForm() ?>
    </p>

    <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                    'id',
                    'username',
                    'email:email',

                    [
                            'label' => 'Role',
                            'value' => function () use ($model) {
                                $roles = Yii::$app->authManager
                                        ->getRolesByUser($model->id);

                                if (isset($roles['administrador'])) {
                                    return 'Administrador';
                                }

                                if (isset($roles['gestorAgregado'])) {
                                    return 'Gestor de Agregado';
                                }

                                if (isset($roles['utilizador'])) {
                                    return 'Utilizador';
                                }

                                return 'Sem role';
                            },
                    ],

                    [
                            'attribute' => 'status',
                            'value' => $model->status === User::STATUS_ACTIVE
                                    ? 'Ativo'
                                    : 'Inativo',
                    ],
                    'created_at:datetime',
                    'updated_at:datetime',
            ],
    ]) ?>

</div>
