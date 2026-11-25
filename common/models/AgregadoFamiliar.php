<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "agregados_familiares".
 *
 * @property int $id
 * @property string $nome
 * @property int $gestor_id
 */
class AgregadoFamiliar extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'agregados_familiares';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nome', 'gestor_id'], 'required'],
            [['gestor_id'], 'integer'],
            [['nome'], 'string', 'max' => 150],
            [['gestor_id'], 'unique'],
            [['gestor_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['gestor_id' => 'id']],
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
            'gestor_id' => 'Gestor ID',
        ];
    }

    public static function doUtilizador(int $utilizadorId): ?self
    {
        $agregadoId = MembroAgregado::find()
            ->select('agregado_id')
            ->where(['utilizador_id' => $utilizadorId])
            ->scalar();

        return $agregadoId === false ? null : self::findOne((int) $agregadoId);
    }

    public function criarPara(int $utilizadorId): bool
    {
        $this->gestor_id = $utilizadorId;

        if (is_string($this->nome)) {
            $this->nome = trim($this->nome);
        }

        return self::getDb()->transaction(function ($db) use ($utilizadorId): bool {
            // Dois pedidos da mesma conta não devem criar dois agregados.
            $db->createCommand(
                'SELECT [[id]] FROM {{%user}} WHERE [[id]] = :id FOR UPDATE',
                [':id' => $utilizadorId]
            )->queryScalar();

            if (self::doUtilizador($utilizadorId) !== null) {
                $this->addError('nome', 'Já pertences a um agregado familiar.');
                return false;
            }

            if (!$this->save()) {
                return false;
            }

            $membro = new MembroAgregado();
            $membro->agregado_id = $this->id;
            $membro->utilizador_id = $utilizadorId;

            if ($membro->hasAttribute('data_entrada')) {
                $membro->data_entrada = date('Y-m-d H:i:s');
            }

            if (!$membro->save()) {
                throw new \RuntimeException(
                    'Não foi possível associar o gestor ao agregado.'
                );
            }

            Despensa::garantirPessoal($utilizadorId);

            $despensa = new Despensa();
            $despensa->nome = $this->nome;
            $despensa->agregado_id = $this->id;
            $despensa->utilizador_id = null;

            if (!$despensa->save()) {
                throw new \RuntimeException(
                    'Não foi possível criar a despensa familiar.'
                );
            }

            $auth = \Yii::$app->authManager;
            $role = $auth->getRole('gestorAgregado');

            if ($role === null) {
                throw new \RuntimeException('A role gestorAgregado não existe no RBAC.');
            }

            if ($auth->getAssignment($role->name, $utilizadorId) === null) {
                $auth->assign($role, $utilizadorId);
            }

            return true;
        });
    }

    public function retirarMembro(int $utilizadorId): bool
    {
        // O gestor não pode ser removido nem sair por esta operação.
        if ((int) $this->gestor_id === $utilizadorId) {
            return false;
        }

        return self::getDb()->transaction(
            function ($db) use ($utilizadorId): bool {

                $membro = MembroAgregado::findOne([
                    'agregado_id' => $this->id,
                    'utilizador_id' => $utilizadorId,
                ]);

                if ($membro === null) {
                    return false;
                }

                if ($membro->delete() !== 1) {
                    return false;
                }

                // Recupera a despensa pessoal ou cria-a, se necessário.
                Despensa::garantirPessoal($utilizadorId);

                if (!Despensa::find()
                    ->where([
                        'utilizador_id' => $utilizadorId,
                        'agregado_id' => null,
                    ])
                    ->exists()) {
                    throw new \RuntimeException(
                        'Não foi possível recuperar a despensa pessoal.'
                    );
                }

                return true;
            }
        );
    }

    public function eliminarComDados(): bool
    {
        $agregadoId = (int) $this->id;
        $gestorId = (int) $this->gestor_id;

        return self::getDb()->transaction(
            function ($db) use ($agregadoId, $gestorId): bool {

                // Bloquear o agregado durante a eliminação.
                $gestorAtual = $db->createCommand(
                    'SELECT gestor_id
                 FROM {{%agregados_familiares}}
                 WHERE id = :id
                 FOR UPDATE',
                    [':id' => $agregadoId]
                )->queryScalar();

                if ($gestorAtual === false ||
                    (int) $gestorAtual !== $gestorId) {
                    return false;
                }

                // Obter os utilizadores associados ao agregado.
                $membroIds = MembroAgregado::find()
                    ->select('utilizador_id')
                    ->where(['agregado_id' => $agregadoId])
                    ->column();

                $membroIds[] = $gestorId;

                // Garantir as despensas pessoais antes da remoção.
                foreach (array_unique(array_map('intval', $membroIds)) as $id) {
                    Despensa::garantirPessoal($id);

                    if (!Despensa::find()
                        ->where([
                            'utilizador_id' => $id,
                            'agregado_id' => null,
                        ])
                        ->exists()) {
                        throw new \RuntimeException(
                            'Não foi possível garantir a despensa pessoal.'
                        );
                    }
                }

                // Obter as despensas que pertencem ao agregado.
                $despensaIds = Despensa::find()
                    ->select('id')
                    ->where(['agregado_id' => $agregadoId])
                    ->column();

                foreach ($despensaIds as $despensaId) {
                    $despensaId = (int) $despensaId;

                    // Apagar movimentos dos itens familiares.
                    $db->createCommand(
                        'DELETE FROM {{%movimentos_stocks}}
                     WHERE item_id IN (
                         SELECT id
                         FROM {{%itens_despensa}}
                         WHERE despensa_id = :despensa
                     )',
                        [':despensa' => $despensaId]
                    )->execute();

                    // Apagar itens familiares.
                    $db->createCommand()->delete(
                        '{{%itens_despensa}}',
                        ['despensa_id' => $despensaId]
                    )->execute();

                    // Apagar a despensa familiar.
                    $db->createCommand()->delete(
                        '{{%despensas}}',
                        ['id' => $despensaId]
                    )->execute();
                }

                // Remover as associações dos membros.
                MembroAgregado::deleteAll([
                    'agregado_id' => $agregadoId,
                ]);

                // Eliminar o próprio agregado.
                if ($this->delete() !== 1) {
                    throw new \RuntimeException(
                        'Não foi possível eliminar o agregado.'
                    );
                }

                // Retirar a role de gestor ao antigo gestor.
                $auth = \Yii::$app->authManager;
                $role = $auth->getRole('gestorAgregado');

                if ($role === null) {
                    throw new \RuntimeException(
                        'A role gestorAgregado não existe.'
                    );
                }

                if ($auth->getAssignment($role->name, $gestorId) !== null) {
                    if (!$auth->revoke($role, $gestorId)) {
                        throw new \RuntimeException(
                            'Não foi possível retirar a role de gestor.'
                        );
                    }
                }

                return true;
            }
        );
    }
}
