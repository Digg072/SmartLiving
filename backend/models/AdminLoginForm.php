<?php

namespace backend\models;

use common\models\LoginForm;
use Yii;

class AdminLoginForm extends LoginForm
{
    public function loginAsAdministrator(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $user = $this->getUser();

        if ($user === null ||
            !Yii::$app->authManager->checkAccess($user->getId(), 'administrador')
        ) {
            $this->addError('password', 'Não foi possível iniciar sessão no Back-Office.');
            return false;
        }

        return Yii::$app->user->login(
            $user,
            $this->rememberMe ? 3600 * 24 * 30 : 0
        );
    }
}