<?php

use yii\db\Migration;

class m261108_150806_criar_despensas_pessoais_existentes extends Migration
{
    /**
     * {@inheritdoc}
     */

    public function safeUp()
    {
        $userIds = (new \yii\db\Query())
            ->select('id')
            ->from('{{%user}}')
            ->where(['status' => [9, 10]])
            ->column($this->db);

        foreach ($userIds as $userId) {

            $existe = (new \yii\db\Query())
                ->from('{{%despensas}}')
                ->where(['utilizador_id' => $userId])
                ->exists($this->db);

            if (!$existe) {
                $this->insert('{{%despensas}}', [
                    'nome' => 'Despensa Pessoal',
                    'utilizador_id' => $userId,
                    'agregado_id' => null,
                ]);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "Esta migration não pode ser revertida automaticamente.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m261108_150806_criar_despensas_pessoais_existentes cannot be reverted.\n";

        return false;
    }
    */
}
