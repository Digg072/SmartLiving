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
    public function rules(): array
    {
        return [
            [['validade'], 'default', 'value' => null],
            [['quantidade'], 'default', 'value' => 0.00],
            [['nome', 'unidade', 'despensa_id', 'categoria_id'], 'required'],
            [['quantidade'], 'required'],
            [['quantidade'], 'number', 'min' => 0],
            [['quantidade'], 'validarQuantidadeUnidade'],
            [['validade'], 'default', 'value' => null],
            [['validade'], 'date', 'format' => 'php:Y-m-d'],
            [['despensa_id', 'categoria_id'], 'integer'],
            [['nome'], 'string', 'max' => 150],
            [['unidade'], 'string', 'max' => 30],
            [['unidade'], 'in', 'range' => [
                'un', 'kg', 'g', 'L', 'ml', 'pacote', 'lata'
            ]],
            [['categoria_id'], 'exist', 'skipOnError' => true, 'targetClass' => Categoria::class, 'targetAttribute' => ['categoria_id' => 'id']],
            [['despensa_id'], 'exist', 'skipOnError' => true, 'targetClass' => Despensa::class, 'targetAttribute' => ['despensa_id' => 'id']],
            ['nome', 'validarNomeDuplicadoNaEdicao'],
        ];
    }


    public function validarQuantidadeUnidade($attribute, $params): void
    {
        $unidadesInteiras = ['un', 'pacote', 'lata'];

        if (
            in_array($this->unidade, $unidadesInteiras, true)
            && is_numeric($this->$attribute)
        ) {
            $quantidade = (float) $this->$attribute;

            if (abs($quantidade - round($quantidade)) > 0.000001) {
                $this->addError(
                    $attribute,
                    'Esta unidade apenas permite quantidades inteiras.'
                );
            }
        }
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
            'categoria_id' => 'Categoria',
        ];
    }


    public static function normalizarNome(string $nome): string
    {
        // Ignorar acentos comuns em português
        $nome = strtr($nome, [
            'á' => 'a', 'à' => 'a', 'â' => 'a', 'ã' => 'a',
            'Á' => 'a', 'À' => 'a', 'Â' => 'a', 'Ã' => 'a',
            'é' => 'e', 'ê' => 'e', 'É' => 'e', 'Ê' => 'e',
            'í' => 'i', 'Í' => 'i',
            'ó' => 'o', 'ô' => 'o', 'õ' => 'o',
            'Ó' => 'o', 'Ô' => 'o', 'Õ' => 'o',
            'ú' => 'u', 'Ú' => 'u',
            'ç' => 'c', 'Ç' => 'c',
        ]);

        // Ignorar espaços e maiúsculas
        $nome = preg_replace('/[\s\p{Z}]+/u', '', $nome) ?? $nome;

        $nome = strtolower($nome);

        return preg_replace('/^(.{2,}[aeiou])s$/u', '$1', $nome) ?? $nome;
    }

    public static function adicionarOuAcumular(
        self $novo,
        int $utilizadorId
    ): array {
        $db = static::getDb();
        $transacao = $db->beginTransaction();

        try {
            // Bloquear a despensa durante a operação
            $despensaId = $db->createCommand(
                'SELECT id FROM {{%despensas}}
             WHERE id = :id FOR UPDATE',
                [':id' => $novo->despensa_id]
            )->queryScalar();

            if ($despensaId === false) {
                throw new \RuntimeException('Despensa não encontrada.');
            }

            // Procurar produtos equivalentes apenas nesta despensa
            $existente = null;

            $itens = self::find()
                ->where(['despensa_id' => $novo->despensa_id])
                ->all();

            foreach ($itens as $item) {
                if (
                    self::normalizarNome($item->nome) ===
                    self::normalizarNome($novo->nome)
                ) {
                    $existente = $item;
                    break;
                }
            }

            if ($existente !== null) {

                $unidadeDiferente =
                    $novo->unidade !== $existente->unidade;

                $categoriaDiferente =
                    (int) $novo->categoria_id !==
                    (int) $existente->categoria_id;

                // Não guardar se unidade ou categoria forem diferentes
                if ($unidadeDiferente || $categoriaDiferente) {
                    $transacao->rollBack();

                    return [
                        'estado' => 'incompativel',
                        'item' => $existente,
                        'unidadeDiferente' => $unidadeDiferente,
                        'categoriaDiferente' => $categoriaDiferente,
                    ];
                }

                // Somar as quantidades
                $anterior = number_format(
                    (float) $existente->quantidade, 2, '.', ''
                );

                $nova = number_format(
                    (float) $anterior + (float) $novo->quantidade,
                    2, '.', ''
                );

                $validadeDiferente =
                    (string) $novo->validade !==
                    (string) $existente->validade;

                if ($nova !== $anterior) {
                    if ($existente->updateAttributes(
                            ['quantidade' => $nova]
                        ) !== 1) {
                        throw new \RuntimeException(
                            'Não foi possível atualizar a quantidade.'
                        );
                    }

                    // Guardar no histórico de stock
                    $db->createCommand()->insert(
                        '{{%movimentos_stocks}}',
                        [
                            'item_id' => $existente->id,
                            'utilizador_id' => $utilizadorId,
                            'quantidade_anterior' => $anterior,
                            'quantidade_nova' => $nova,
                            'data_movimento' =>
                                new \yii\db\Expression('NOW()'),
                        ]
                    )->execute();
                }

                $transacao->commit();

                return [
                    'estado' => 'acumulado',
                    'id' => $existente->id,
                    'validadeDiferente' => $validadeDiferente,
                ];
            }

            // Não existe: criar um produto novo
            if (!$novo->save(false)) {
                throw new \RuntimeException(
                    'Não foi possível criar o item.'
                );
            }

            $transacao->commit();

            return [
                'estado' => 'criado',
                'id' => $novo->id,
            ];

        } catch (\Throwable $e) {
            if ($transacao->getIsActive()) {
                $transacao->rollBack();
            }

            throw $e;
        }
    }

    public function validarNomeDuplicadoNaEdicao($attribute, $params): void
    {
        if ($this->isNewRecord) {
            return;
        }

        $novoNome = self::normalizarNome($this->$attribute);
        $nomeAnterior = self::normalizarNome($this->getOldAttribute('nome'));

        if ($novoNome === $nomeAnterior) {
            return;
        }

        $nomesNaDespensa = self::find()
            ->select('nome')
            ->where(['despensa_id' => $this->getOldAttribute('despensa_id')])
            ->andWhere(['<>', 'id', $this->getOldPrimaryKey()])
            ->column();

        foreach ($nomesNaDespensa as $nome) {
            if (self::normalizarNome($nome) === $novoNome) {
                $this->addError(
                    $attribute,
                    'Já existe outro item com este nome na tua despensa.'
                );
                return;
            }
        }
    }
}
