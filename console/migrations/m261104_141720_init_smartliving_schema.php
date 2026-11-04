<?php

use yii\db\Migration;

class m261104_141720_init_smartliving_schema extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {

        $engine = $this->db->createCommand(
            "SELECT ENGINE
             FROM information_schema.TABLES
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = 'user'"
        )->queryScalar();

        if (strcasecmp((string) $engine, 'InnoDB') !== 0) {
            $this->execute('ALTER TABLE `user` ENGINE=InnoDB');
        }

        // 1. PERFIL DO UTILIZADOR
        $this->createTable('{{%user_profile}}', [
            'user_id' => $this->integer()->notNull(),
            'nome' => $this->string(150)->notNull(),
            'telefone' => $this->string(20),

        ], 'ENGINE=InnoDB');

        $this->addPrimaryKey(
            'pk-user_profile',
            '{{%user_profile}}',
            'user_id'
        );

        $this->addForeignKey(
            'fk-user_profile-user',
            '{{%user_profile}}', 'user_id',
            '{{%user}}', 'id',
            'RESTRICT', 'CASCADE'
        );


        // 2. AGREGADOS FAMILIARES
        $this->createTable('{{%agregados_familiares}}', [
            'id' => $this->primaryKey(),
            'nome' => $this->string(150)->notNull(),
            'gestor_id' => $this->integer()->notNull(),
        ], 'ENGINE=InnoDB');

        $this->createIndex(
            'ux-agregados-gestor',
            '{{%agregados_familiares}}',
            'gestor_id',
            true
        );

        $this->addForeignKey(
            'fk-agregados-gestor',
            '{{%agregados_familiares}}', 'gestor_id',
            '{{%user}}', 'id',
            'RESTRICT', 'CASCADE'
        );


        // 3. MEMBROS DO AGREGADO
        $this->createTable('{{%membros_agregado}}', [
            'id' => $this->primaryKey(),
            'agregado_id' => $this->integer()->notNull(),
            'utilizador_id' => $this->integer()->notNull(),
        ], 'ENGINE=InnoDB');

        $this->createIndex(
            'idx-membros-agregado',
            '{{%membros_agregado}}',
            'agregado_id'
        );

        $this->createIndex(
            'ux-membros-utilizador',
            '{{%membros_agregado}}',
            'utilizador_id',
            true
        );

        $this->addForeignKey(
            'fk-membros-agregado',
            '{{%membros_agregado}}', 'agregado_id',
            '{{%agregados_familiares}}', 'id',
            'CASCADE', 'CASCADE'
        );

        $this->addForeignKey(
            'fk-membros-user',
            '{{%membros_agregado}}', 'utilizador_id',
            '{{%user}}', 'id',
            'RESTRICT', 'CASCADE'
        );


        // 4. DESPENSAS
        $this->createTable('{{%despensas}}', [
            'id' => $this->primaryKey(),
            'nome' => $this->string(150)->notNull(),
            'utilizador_id' => $this->integer(),
            'agregado_id' => $this->integer(),
        ], 'ENGINE=InnoDB');

        $this->createIndex(
            'ux-despensas-utilizador',
            '{{%despensas}}',
            'utilizador_id',
            true
        );

        $this->createIndex(
            'ux-despensas-agregado',
            '{{%despensas}}',
            'agregado_id',
            true
        );

        $this->addForeignKey(
            'fk-despensas-user',
            '{{%despensas}}', 'utilizador_id',
            '{{%user}}', 'id',
            'RESTRICT', 'RESTRICT'
        );

        $this->addForeignKey(
            'fk-despensas-agregado',
            '{{%despensas}}', 'agregado_id',
            '{{%agregados_familiares}}', 'id',
            'RESTRICT', 'RESTRICT'
        );

        // Exatamente um proprietário: utilizador OU agregado
        $this->execute(
            'ALTER TABLE `despensas`
         ADD CONSTRAINT `ck_despensas_dono`
         CHECK (
             (`utilizador_id` IS NOT NULL
              AND `agregado_id` IS NULL)
             OR
             (`utilizador_id` IS NULL
              AND `agregado_id` IS NOT NULL)
         )'
        );


        // 5. CATEGORIAS
        $this->createTable('{{%categorias}}', [
            'id' => $this->primaryKey(),
            'nome' => $this->string(150)->notNull(),
        ], 'ENGINE=InnoDB');

        $this->createIndex(
            'ux-categorias-nome',
            '{{%categorias}}',
            'nome',
            true
        );


        // 6. ITENS DA DESPENSA
        $this->createTable('{{%itens_despensa}}', [
            'id' => $this->primaryKey(),
            'nome' => $this->string(150)->notNull(),
            'quantidade' => $this->decimal(10, 2)
                ->notNull()->defaultValue(0),
            'unidade' => $this->string(30)->notNull(),
            'validade' => $this->date(),
            'despensa_id' => $this->integer()->notNull(),
            'categoria_id' => $this->integer()->notNull(),
        ], 'ENGINE=InnoDB');

        $this->createIndex(
            'idx-itens-despensa',
            '{{%itens_despensa}}',
            'despensa_id'
        );

        $this->createIndex(
            'idx-itens-categoria',
            '{{%itens_despensa}}',
            'categoria_id'
        );

        $this->addForeignKey(
            'fk-itens-despensa',
            '{{%itens_despensa}}', 'despensa_id',
            '{{%despensas}}', 'id',
            'RESTRICT', 'CASCADE'
        );

        $this->addForeignKey(
            'fk-itens-categoria',
            '{{%itens_despensa}}', 'categoria_id',
            '{{%categorias}}', 'id',
            'RESTRICT', 'CASCADE'
        );


        // 7. MOVIMENTOS DE STOCK
        $this->createTable('{{%movimentos_stocks}}', [
            'id' => $this->primaryKey(),
            'item_id' => $this->integer()->notNull(),
            'utilizador_id' => $this->integer()->notNull(),
            'quantidade_anterior' => $this->decimal(10, 2)
                ->notNull(),
            'quantidade_nova' => $this->decimal(10, 2)
                ->notNull(),
            'data_movimento' => $this->dateTime()->notNull(),
        ], 'ENGINE=InnoDB');

        $this->createIndex(
            'idx-movimentos-item',
            '{{%movimentos_stocks}}',
            'item_id'
        );

        $this->createIndex(
            'idx-movimentos-user',
            '{{%movimentos_stocks}}',
            'utilizador_id'
        );

        $this->addForeignKey(
            'fk-movimentos-item',
            '{{%movimentos_stocks}}', 'item_id',
            '{{%itens_despensa}}', 'id',
            'CASCADE', 'CASCADE'
        );

        $this->addForeignKey(
            'fk-movimentos-user',
            '{{%movimentos_stocks}}', 'utilizador_id',
            '{{%user}}', 'id',
            'RESTRICT', 'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable('{{%movimentos_stocks}}');
        $this->dropTable('{{%itens_despensa}}');
        $this->dropTable('{{%categorias}}');
        $this->dropTable('{{%despensas}}');
        $this->dropTable('{{%membros_agregado}}');
        $this->dropTable('{{%agregados_familiares}}');
        $this->dropTable('{{%user_profile}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m261104_141720_init_smartliving_schema cannot be reverted.\n";

        return false;
    }
    */
}
