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
            [['agregado_id'], 'exist', 'skipOnError' => true, 'targetClass' => AgregadosFamiliares::class, 'targetAttribute' => ['agregado_id' => 'id']],
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

}
