<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "movimentos_stocks".
 *
 * @property int $id
 * @property int $item_id
 * @property int $utilizador_id
 * @property float $quantidade_anterior
 * @property float $quantidade_nova
 * @property string $data_movimento
 *
 * @property ItemDespensa $item
 * @property User $utilizador
 */
class MovimentoStock extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'movimentos_stocks';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['item_id', 'utilizador_id', 'quantidade_anterior', 'quantidade_nova', 'data_movimento'], 'required'],
            [['item_id', 'utilizador_id'], 'integer'],
            [['quantidade_anterior', 'quantidade_nova'], 'number'],
            [['data_movimento'], 'safe'],
            [['item_id'], 'exist', 'skipOnError' => true, 'targetClass' => ItemDespensa::class, 'targetAttribute' => ['item_id' => 'id']],
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
            'item_id' => 'Item ID',
            'utilizador_id' => 'Utilizador ID',
            'quantidade_anterior' => 'Quantidade Anterior',
            'quantidade_nova' => 'Quantidade Nova',
            'data_movimento' => 'Data Movimento',
        ];
    }

    /**
     * Gets query for [[Item]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(
            ItemDespensa::class,
            ['id' => 'item_id']
        );
    }

    public function getUtilizador()
    {
        return $this->hasOne(
            User::class,
            ['id' => 'utilizador_id']
        );
    }

    public static function registar(
        int $itemId,
        int $utilizadorId,
            $quantidadeAnterior,
            $quantidadeNova
    ): void {
        $movimento = new self();

        $movimento->item_id = $itemId;
        $movimento->utilizador_id = $utilizadorId;
        $movimento->quantidade_anterior = $quantidadeAnterior;
        $movimento->quantidade_nova = $quantidadeNova;
        $movimento->data_movimento = date('Y-m-d H:i:s');

        if (!$movimento->save()) {
            throw new \RuntimeException(
                'Não foi possível registar o movimento de stock.'
            );
        }
    }

}
