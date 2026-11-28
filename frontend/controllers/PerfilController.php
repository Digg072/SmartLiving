<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

use common\models\User;
use common\models\UserProfile;
use common\models\AgregadoFamiliar;
use common\models\MembroAgregado;

use frontend\models\PerfilForm;
use frontend\models\ResetPasswordForm;

class PerfilController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'password', 'delete'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['gerirProprioPerfil'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $user = User::findOne((int) Yii::$app->user->id);

        if ($user === null) {
            throw new NotFoundHttpException('Utilizador não encontrado.');
        }

        $perfil = UserProfile::findOne(['user_id' => $user->id]);

        if ($perfil === null) {
            $perfil = new UserProfile();
            $perfil->user_id = $user->id;
        }

        $model = new PerfilForm();
        $model->userId = (int) $user->id;

        $model->nome = $perfil->nome;
        $model->username = $user->username;
        $model->email = $user->email;
        $model->telefone = $perfil->telefone;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $transacao = Yii::$app->db->beginTransaction();

            try {
                $user->username = $model->username;
                $user->email = $model->email;

                $perfil->nome = $model->nome;
                $perfil->telefone = $model->telefone !== ''
                    ? $model->telefone
                    : null;

                if (!$user->save(false) || !$perfil->save(false)) {
                    throw new \RuntimeException(
                        'Não foi possível atualizar o perfil.'
                    );
                }

                $transacao->commit();

                Yii::$app->session->setFlash(
                    'success',
                    'Perfil atualizado com sucesso.'
                );

                return $this->refresh();

            } catch (\Throwable $e) {

                if ($transacao->getIsActive()) {
                    $transacao->rollBack();
                }

                Yii::error($e->getMessage(), __METHOD__);

                Yii::$app->session->setFlash(
                    'error',
                    'Não foi possível guardar as alterações.'
                );
            }
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }

    public function actionPassword()
    {
        $model = new ResetPasswordForm();

        if (
            $model->load(Yii::$app->request->post()) &&
            $model->validate()
        ) {
            $user = User::findOne((int) Yii::$app->user->id);

            if ($user === null) {
                throw new NotFoundHttpException(
                    'Utilizador não encontrado.'
                );
            }

            $user->setPassword($model->passwordNova);
            $user->generateAuthKey();

            if ($user->save(false, ['password_hash', 'auth_key'])) {

                Yii::$app->user->login($user);

                Yii::$app->session->setFlash(
                    'success',
                    'Password alterada com sucesso.'
                );

                return $this->redirect(['/perfil/index']);
            }

            $model->addError(
                'passwordNova',
                'Não foi possível alterar a password.'
            );
        }

        return $this->render('password', [
            'model' => $model,
        ]);
    }

    public function actionDelete()
    {
        $userId = (int) Yii::$app->user->id;

        $user = User::findOne($userId);

        if ($user === null) {
            throw new NotFoundHttpException(
                'Utilizador não encontrado.'
            );
        }

        // Impedir eliminação da própria conta administradora.
        $roles = Yii::$app->authManager->getRolesByUser($userId);

        if (isset($roles['administrador'])) {
            Yii::$app->session->setFlash(
                'error',
                'A conta de administrador não pode ser eliminada aqui.'
            );

            return $this->redirect(['index']);
        }

        // Verificar se gere ou pertence a um agregado.
        $eGestor = AgregadoFamiliar::find()
            ->where(['gestor_id' => $userId])
            ->exists();

        $eMembro = MembroAgregado::find()
            ->where(['utilizador_id' => $userId])
            ->exists();

        if ($eGestor || $eMembro) {
            Yii::$app->session->setFlash(
                'error',
                'Antes de eliminar a conta, deves sair ou eliminar o teu agregado familiar.'
            );

            return $this->redirect(['index']);
        }

        // Soft Delete: manter o registo na base de dados.
        $user->status = User::STATUS_DELETED;

        if (!$user->save(false, ['status'])) {
            Yii::$app->session->setFlash(
                'error',
                'Não foi possível eliminar a conta.'
            );

            return $this->redirect(['index']);
        }

        Yii::$app->user->logout();

        Yii::$app->session->setFlash(
            'success',
            'A tua conta foi desativada com sucesso.'
        );

        return $this->redirect(['/site/index']);
    }
}