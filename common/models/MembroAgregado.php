<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "membros_agregado".
 *
 * @property int $id
 * @property int $agregado_id
 * @property int $utilizador_id
 */
class MembroAgregado extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'membros_agregado';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['agregado_id', 'utilizador_id'], 'required'],
            [['agregado_id', 'utilizador_id'], 'integer'],
            [['utilizador_id'], 'unique'],
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
            'agregado_id' => 'Agregado ID',
            'utilizador_id' => 'Utilizador ID',
        ];
    }

}
