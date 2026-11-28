<?php

namespace frontend\models;

use yii\base\Model;
use common\models\User;

class PerfilForm extends Model
{
    public $nome;
    public $username;
    public $email;
    public $telefone;

    public $userId;

    public function rules()
    {
        return [
            [['nome', 'username', 'email', 'telefone'], 'trim'],

            [['nome', 'username', 'email'], 'required'],

            ['nome', 'string', 'max' => 150],

            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'email'],
            ['email', 'string', 'max' => 255],

            ['telefone', 'string', 'max' => 20],

            [
                'username',
                'unique',
                'targetClass' => User::class,
                'targetAttribute' => 'username',
                'filter' => ['<>', 'id', $this->userId],
                'message' => 'Este username já está em utilização.',
            ],

            [
                'email',
                'unique',
                'targetClass' => User::class,
                'targetAttribute' => 'email',
                'filter' => ['<>', 'id', $this->userId],
                'message' => 'Este email já está em utilização.',
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'nome' => 'Nome de Utilizador',
            'username' => 'Username',
            'email' => 'Email',
            'telefone' => 'Telefone',
        ];
    }
}