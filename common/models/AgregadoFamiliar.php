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
}
