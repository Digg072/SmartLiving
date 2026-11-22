<?php

namespace frontend\models;

use common\models\MembroAgregado;
use common\models\User;
use yii\base\Model;
use yii\db\IntegrityException;

class AdicionarMembroForm extends Model
{
    public $username = '';

    public function rules(): array
    {
        return [
            [['username'], 'trim'],
            [['username'], 'required'],
            [['username'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'username' => 'Nome de utilizador',
        ];
    }

    public function adicionar(int $agregadoId): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $utilizador = User::find()
            ->where([
                'username' => $this->username,
                'status' => User::STATUS_ACTIVE,
            ])
            ->one();

        if ($utilizador === null) {
            $this->addError(
                'username',
                'Não foi encontrado nenhum utilizador ativo com esse nome.'
            );
            return false;
        }

        if (MembroAgregado::find()
            ->where(['utilizador_id' => $utilizador->id])
            ->exists()) {

            $this->addError(
                'username',
                'Este utilizador já pertence a um agregado familiar.'
            );
            return false;
        }

        $membro = new MembroAgregado();
        $membro->agregado_id = $agregadoId;
        $membro->utilizador_id = $utilizador->id;

        try {
            if ($membro->save()) {
                return true;
            }
        } catch (IntegrityException $e) {
            $this->addError(
                'username',
                'Não foi possível adicionar o utilizador. Verifica se já pertence a um agregado.'
            );
            return false;
        }

        $this->addError(
            'username',
            'Não foi possível adicionar o utilizador.'
        );

        return false;
    }
}