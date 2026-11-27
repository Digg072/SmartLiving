<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "despensas".
 *
 * @property int $id
 * @property string $nome
 * @property int|null $utilizador_id
 * @property int|null $agregado_id
 */
class Despensa extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'despensas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['utilizador_id', 'agregado_id'], 'default', 'value' => null],
            [['nome'], 'required'],
            [['utilizador_id', 'agregado_id'], 'integer'],
            [['nome'], 'string', 'max' => 150],
            [['utilizador_id'], 'unique'],
            [['agregado_id'], 'unique'],
            [['agregado_id'], 'exist', 'skipOnError' => true, 'targetClass' => AgregadoFamiliar::class, 'targetAttribute' => ['agregado_id' => 'id']],
            [['utilizador_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['utilizador_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome',
            'utilizador_id' => 'Utilizador ID',
            'agregado_id' => 'Agregado ID',
        ];
    }


    public static function garantirPessoal(int $userId): self
    {
        // Procurar uma despensa pessoal existente
        $despensa = self::find()
            ->where([
                'utilizador_id' => $userId,
                'agregado_id' => null,
            ])
            ->one();

        if ($despensa !== null) {
            return $despensa;
        }

        // Criar apenas se ainda não existir
        $despensa = new self();
        $despensa->nome = 'Despensa Pessoal';
        $despensa->utilizador_id = $userId;
        $despensa->agregado_id = null;

        if (!$despensa->save()) {
            throw new \RuntimeException(
                'Não foi possível criar a despensa pessoal.'
            );
        }

        return $despensa;
    }


    public static function obterAtivaPara(int $userId): ?self
    {
        // Garantir que existe uma despensa pessoal.
        $despensaPessoal = self::garantirPessoal($userId);

        // Verificar se o utilizador pertence a um agregado.
        $membro = MembroAgregado::findOne([
            'utilizador_id' => $userId,
        ]);

        $agregadoId = $membro !== null
            ? (int) $membro->agregado_id
            : null;

        // Verificar também se é gestor de um agregado.
        if ($agregadoId === null) {
            $agregado = AgregadoFamiliar::findOne([
                'gestor_id' => $userId,
            ]);

            if ($agregado !== null) {
                $agregadoId = (int) $agregado->id;
            }
        }

        // Se pertence a um agregado, utilizar a despensa familiar.
        if ($agregadoId !== null) {
            return self::findOne([
                'agregado_id' => $agregadoId,
            ]);
        }

        // Caso contrário, utilizar a despensa pessoal.
        return $despensaPessoal;
    }
}
