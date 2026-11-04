<?php

use yii\db\Migration;

class m261104_143123_fix_smartliving_foreign_keys extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        // Converter para InnoDB as tabelas necessárias.
        // A tabela user só é convertida se ainda não usar InnoDB.
        $tables = [
            'user',
            'user_profile',
            'agregados_familiares',
            'membros_agregado',
            'despensas',
            'categorias',
            'itens_despensa',
            'movimentos_stocks',
        ];

        foreach ($tables as $table) {
            $engine = $this->db->createCommand(
                'SELECT ENGINE
             FROM information_schema.TABLES
             WHERE TABLE_SCHEMA = DATABASE()
             AND TABLE_NAME = :table',
                [':table' => $table]
            )->queryScalar();

            if ($engine === false) {
                throw new \RuntimeException(
                    "Tabela não encontrada: $table"
                );
            }

            if (strcasecmp($engine, 'InnoDB') !== 0) {
                $this->execute(
                    "ALTER TABLE `$table` ENGINE=InnoDB"
                );
            }
        }

        // Foreign Keys previstas no DER do SmartLiving
        $foreignKeys = [
            [
                'fk-user_profile-user',
                'user_profile', 'user_id',
                'user', 'id', 'RESTRICT', 'CASCADE'
            ],
            [
                'fk-agregados-gestor',
                'agregados_familiares', 'gestor_id',
                'user', 'id', 'RESTRICT', 'CASCADE'
            ],
            [
                'fk-membros-agregado',
                'membros_agregado', 'agregado_id',
                'agregados_familiares', 'id',
                'CASCADE', 'CASCADE'
            ],
            [
                'fk-membros-user',
                'membros_agregado', 'utilizador_id',
                'user', 'id', 'RESTRICT', 'CASCADE'
            ],
            [
                'fk-despensas-user',
                'despensas', 'utilizador_id',
                'user', 'id',
                'RESTRICT', 'RESTRICT'
            ],
            [
                'fk-despensas-agregado',
                'despensas', 'agregado_id',
                'agregados_familiares', 'id',
                'RESTRICT', 'RESTRICT'
            ],
            [
                'fk-itens-despensa',
                'itens_despensa', 'despensa_id',
                'despensas', 'id', 'RESTRICT', 'CASCADE'
            ],
            [
                'fk-itens-categoria',
                'itens_despensa', 'categoria_id',
                'categorias', 'id', 'RESTRICT', 'CASCADE'
            ],
            [
                'fk-movimentos-item',
                'movimentos_stocks', 'item_id',
                'itens_despensa', 'id',
                'CASCADE', 'CASCADE'
            ],
            [
                'fk-movimentos-user',
                'movimentos_stocks', 'utilizador_id',
                'user', 'id', 'RESTRICT', 'CASCADE'
            ],
        ];

        foreach ($foreignKeys as $fk) {
            [
                $name, $table, $column,
                $refTable, $refColumn, $onDelete, $onUpdate
            ] = $fk;

            $exists = $this->db->createCommand(
                "SELECT COUNT(*)
             FROM information_schema.TABLE_CONSTRAINTS
             WHERE TABLE_SCHEMA = DATABASE()
             AND TABLE_NAME = :table
             AND CONSTRAINT_NAME = :name
             AND CONSTRAINT_TYPE = 'FOREIGN KEY'",
                [
                    ':table' => $table,
                    ':name' => $name,
                ]
            )->queryScalar();

            if (!$exists) {
                $this->addForeignKey(
                    $name,
                    $table,
                    $column,
                    $refTable,
                    $refColumn,
                    $onDelete,
                    $onUpdate
                );
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        // Reverter apenas as Foreign Keys desta correção.
        // Não voltar ao motor MyISAM.
        $foreignKeys = [
            ['fk-movimentos-user', 'movimentos_stocks'],
            ['fk-movimentos-item', 'movimentos_stocks'],
            ['fk-itens-categoria', 'itens_despensa'],
            ['fk-itens-despensa', 'itens_despensa'],
            ['fk-despensas-agregado', 'despensas'],
            ['fk-despensas-user', 'despensas'],
            ['fk-membros-user', 'membros_agregado'],
            ['fk-membros-agregado', 'membros_agregado'],
            ['fk-agregados-gestor', 'agregados_familiares'],
            ['fk-user_profile-user', 'user_profile'],
        ];

        foreach ($foreignKeys as [$name, $table]) {
            $this->dropForeignKey($name, $table);
        }
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m261104_143123_fix_smartliving_foreign_keys cannot be reverted.\n";

        return false;
    }
    */
}
