<?php

namespace backend\models;

use common\models\User;
use Yii;
use yii\base\Model;

class UserForm extends Model
{
    public $id;
    public $username = '';
    public $email = '';
    public $password = '';
    public $status = User::STATUS_ACTIVE;
    public $role = 'utilizador';

    public function rules(): array
    {
        return [
            [['username', 'email', 'status', 'role'], 'required'],

            [['username', 'email'], 'trim'],

            ['username', 'string', 'min' => 2, 'max' => 255],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],

            ['username', 'unique',
                'targetClass' => User::class,
                'filter' => function ($query) {
                    if ($this->id !== null) {
                        $query->andWhere(['<>', 'id', $this->id]);
                    }
                },
            ],

            ['email', 'unique',
                'targetClass' => User::class,
                'filter' => function ($query) {
                    if ($this->id !== null) {
                        $query->andWhere(['<>', 'id', $this->id]);
                    }
                },
            ],

            ['password', 'required', 'on' => 'create'],
            ['password', 'string', 'min' =>
                Yii::$app->params['user.passwordMinLength'] ?? 8
            ],

            ['status', 'in', 'range' => [
                User::STATUS_ACTIVE,
                User::STATUS_INACTIVE,
            ]],

            ['role', 'in', 'range' => [
                'utilizador',
                'gestorAgregado',
                'administrador',
            ]],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'username' => 'Nome de utilizador',
            'email' => 'Email',
            'password' => 'Password',
            'status' => 'Estado',
            'role' => 'Perfil',
        ];
    }

    public function loadFromUser(User $user): void
    {
        $this->id = $user->id;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->status = $user->status;

        $roles = Yii::$app->authManager->getRolesByUser($user->id);

        if (isset($roles['administrador'])) {
            $this->role = 'administrador';
        } elseif (isset($roles['gestorAgregado'])) {
            $this->role = 'gestorAgregado';
        } else {
            $this->role = 'utilizador';
        }
    }

    public function saveUser(?User $user = null): User|false
    {
        if (!$this->validate()) {
            return false;
        }

        $isNew = ($user === null);
        $user = $user ?? new User();

        $auth = Yii::$app->authManager;
        $role = $auth->getRole($this->role);

        if ($role === null) {
            throw new \RuntimeException('Perfil não encontrado.');
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            $user->username = $this->username;
            $user->email = $this->email;
            $user->status = (int) $this->status;

            if ($isNew) {
                $user->generateAuthKey();
            }

            if ($this->password !== '') {
                $user->setPassword($this->password);

                if (!$isNew) {
                    $user->generateAuthKey();
                    $user->removePasswordResetToken();
                }
            }

            if (!$user->save(false)) {
                $transaction->rollBack();
                return false;
            }

            $auth->revokeAll($user->id);
            $auth->assign($role, $user->id);

            $transaction->commit();

            return $user;

        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
}
