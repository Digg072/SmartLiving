<?php

namespace frontend\models;

use Yii;
use yii\base\Model;

class ResetPasswordForm extends Model
{
    public $passwordAtual;
    public $passwordNova;
    public $passwordConfirmacao;

    public function rules()
    {
        return [
            [
                [
                    'passwordAtual',
                    'passwordNova',
                    'passwordConfirmacao',
                ],
                'required',
            ],

            ['passwordNova', 'string', 'min' => 8],

            [
                'passwordConfirmacao',
                'compare',
                'compareAttribute' => 'passwordNova',
                'message' => 'As passwords não coincidem.',
            ],

            ['passwordAtual', 'validarPasswordAtual'],
        ];
    }

    public function validarPasswordAtual($attribute)
    {
        $user = Yii::$app->user->identity;

        if (
            $user === null ||
            !$user->validatePassword($this->$attribute)
        ) {
            $this->addError(
                $attribute,
                'A password atual está incorreta.'
            );
        }
    }

    public function attributeLabels()
    {
        return [
            'passwordAtual' => 'Password atual',
            'passwordNova' => 'Nova password',
            'passwordConfirmacao' => 'Confirmar nova password',
        ];
    }
}