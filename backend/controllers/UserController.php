<?php

namespace backend\controllers;

use Yii;
use common\models\User;
use backend\models\UserForm;
use backend\models\UserSearch;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;

class UserController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['gerirUtilizadores'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'restore' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(
            Yii::$app->request->queryParams
        );

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        $model = new UserForm([
            'scenario' => 'create',
        ]);

        if ($model->load(Yii::$app->request->post())) {
            $user = $model->saveUser();

            if ($user !== false) {
                return $this->redirect([
                    'view',
                    'id' => $user->id,
                ]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $user = $this->findModel($id);

        $model = new UserForm();
        $model->loadFromUser($user);

        if ($model->load(Yii::$app->request->post())) {

            // Impedir que o administrador bloqueie
            // a própria conta ou retire a sua role.
            if (
                (int) $user->id === (int) Yii::$app->user->id
                && (
                    (int) $model->status !== User::STATUS_ACTIVE
                    || $model->role !== 'administrador'
                )
            ) {
                $model->addError(
                    'role',
                    'Não podes inativar a tua conta ou retirar a tua role de administrador.'
                );
            } else {
                if ($model->saveUser($user) !== false) {
                    return $this->redirect([
                        'view',
                        'id' => $user->id,
                    ]);
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        if ((int) $id === (int) Yii::$app->user->id) {
            throw new ForbiddenHttpException(
                'Não podes eliminar a tua própria conta.'
            );
        }

        $user = $this->findModel($id);

        $user->status = User::STATUS_DELETED;

        if (!$user->save(false)) {
            throw new \RuntimeException(
                'Não foi possível eliminar o utilizador.'
            );
        }

        return $this->redirect(['index']);
    }


    public function actionDeleted()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => User::find()
                ->where(['status' => User::STATUS_DELETED])
                ->orderBy(['id' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        return $this->render('deleted', [
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionRestore($id)
    {
        $user = User::find()
            ->where([
                'id' => $id,
                'status' => User::STATUS_DELETED,
            ])
            ->one();

        if ($user === null) {
            throw new NotFoundHttpException(
                'Utilizador eliminado não encontrado.'
            );
        }

        $user->status = User::STATUS_ACTIVE;

        if (!$user->save(false)) {
            throw new \RuntimeException(
                'Não foi possível restaurar o utilizador.'
            );
        }

        Yii::$app->session->setFlash(
            'success',
            'Utilizador restaurado com sucesso.'
        );

        return $this->redirect(['deleted']);
    }

    protected function findModel($id)
    {
        $model = User::find()
            ->where(['id' => $id])
            ->andWhere([
                '<>',
                'status',
                User::STATUS_DELETED,
            ])
            ->one();

        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException(
            'Utilizador não encontrado.'
        );
    }
}