<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "itens_despensa".
 *
 * @property int $id
 * @property string $nome
 * @property float $quantidade
 * @property string $unidade
 * @property string|null $validade
 * @property int $despensa_id
 * @property int $categoria_id
 */
class ItemDespensa extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'itens_despensa';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['validade'], 'default', 'value' => null],
            [['quantidade'], 'default', 'value' => 0.00],
            [['nome', 'unidade', 'despensa_id', 'categoria_id'], 'required'],
            [['quantidade'], 'number'],
            [['validade'], 'safe'],
            [['despensa_id', 'categoria_id'], 'integer'],
            [['nome'], 'string', 'max' => 150],
            [['unidade'], 'string', 'max' => 30],
            [['categoria_id'], 'exist', 'skipOnError' => true, 'targetClass' => Categorias::class, 'targetAttribute' => ['categoria_id' => 'id']],
            [['despensa_id'], 'exist', 'skipOnError' => true, 'targetClass' => Despensas::class, 'targetAttribute' => ['despensa_id' => 'id']],
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
            'quantidade' => 'Quantidade',
            'unidade' => 'Unidade',
            'validade' => 'Validade',
            'despensa_id' => 'Despensa ID',
            'categoria_id' => 'Categoria ID',
        ];
    }

}
