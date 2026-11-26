<?php

use yii\helpers\Html;
use yii\web\View;

/** @var View $this */
/** @var common\models\Despensa|null $despensa */
/** @var string $tipoDespensa */
/** @var common\models\ItemDespensa[] $itens */
/** @var common\models\Categoria[] $categorias */
/** @var bool $porCategoria */
/** @var string $hoje */
/** @var string $limiteAviso */
/** @var string $ordenacao */
/** @var array $opcoesOrdenacao */

$this->title = 'A Minha Despensa';

// Reutiliza as categorias já enviadas pelo DespensaController.
$categoriasOrdenadas = array_values($categorias);
usort($categoriasOrdenadas, static function ($a, $b) {
    return strcasecmp($a->nome, $b->nome);
});
?>

<div class="despensa-index container py-4">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if ($despensa === null): ?>

        <div class="alert alert-info mt-4">
            Ainda não existe uma despensa associada à tua conta.
        </div>

    <?php else: ?>

        <div class="card my-4">
            <div class="card-body">
                <h4><?= Html::encode($despensa->nome) ?></h4>
                <p class="mb-0">
                    Tipo de despensa:
                    <strong><?= Html::encode($tipoDespensa) ?></strong>
                </p>
            </div>
        </div>

        <!-- Adicionar item e ordenação existente -->
        <div class="position-relative d-flex align-items-center gap-2 my-3">
            <?= Html::a('Adicionar Item', ['/item/create'], [
                    'class' => 'btn btn-primary',
            ]) ?>

            <details>
                <summary class="btn btn-outline-secondary d-inline-flex align-items-center"
                         title="Ordenar itens">
                    <i class="bi bi-funnel" aria-hidden="true"></i>
                    <span class="visually-hidden">Opções de ordenação</span>
                </summary>

                <div class="position-absolute top-100 start-0 mt-2 p-3 bg-body border rounded shadow-sm"
                     style="width: 15rem; max-width: 100%; z-index: 10;">
                    <?= Html::beginForm(['/despensa/index'], 'get') ?>

                    <?= Html::label('Ordenar por', 'ordenar-itens', [
                            'class' => 'form-label fw-semibold',
                    ]) ?>

                    <?= Html::dropDownList('ordenar', $ordenacao, $opcoesOrdenacao, [
                            'id' => 'ordenar-itens',
                            'class' => 'form-select form-select-sm',
                    ]) ?>

                    <?= Html::submitButton('Aplicar', [
                            'class' => 'btn btn-primary btn-sm w-100 mt-2',
                    ]) ?>

                    <?= Html::endForm() ?>
                </div>
            </details>
        </div>

        <!-- Novos filtros: aplicados imediatamente sem recarregar a página -->
        <div class="row g-3 mb-4 align-items-end">
            <div class="col-md-7">
                <?= Html::label('Pesquisar produtos', 'pesquisa-produtos', [
                        'class' => 'form-label fw-semibold',
                ]) ?>

                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-search" aria-hidden="true"></i>
                    </span>
                    <?= Html::input('search', null, '', [
                            'id' => 'pesquisa-produtos',
                            'class' => 'form-control',
                            'placeholder' => 'Pesquisar pelo nome...',
                            'autocomplete' => 'off',
                    ]) ?>
                </div>
            </div>

            <div class="col-md-5">
                <?= Html::label('Categoria', 'filtro-categoria', [
                        'class' => 'form-label fw-semibold',
                ]) ?>

                <select id="filtro-categoria" class="form-select">
                    <option value="">Todas as categorias</option>
                    <?php foreach ($categoriasOrdenadas as $categoria): ?>
                        <option value="<?= (int) $categoria->id ?>">
                            <?= Html::encode($categoria->nome) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <?php if (empty($itens)): ?>

            <div class="alert alert-info">
                A tua despensa ainda não contém itens.
            </div>

        <?php else: ?>

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Quantidade</th>
                        <th>Unidade</th>
                        <th>Categoria</th>
                        <th>Validade</th>
                        <th class="text-end">Ações</th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php $categoriaAnterior = null; ?>

                    <?php foreach ($itens as $item): ?>
                        <?php $categoriaId = (int) $item->categoria_id; ?>

                        <?php if ($porCategoria && $categoriaId !== $categoriaAnterior): ?>
                            <?php $categoriaAnterior = $categoriaId; ?>
                            <tr class="table-light js-titulo-categoria"
                                data-categoria="<?= $categoriaId ?>">
                                <th colspan="6">
                                    <?= Html::encode($categorias[$categoriaId]->nome ?? 'Sem categoria') ?>
                                </th>
                            </tr>
                        <?php endif; ?>

                        <tr class="js-item-despensa"
                            data-nome="<?= Html::encode($item->nome) ?>"
                            data-categoria="<?= $categoriaId ?>">
                            <td><?= Html::encode($item->nome) ?></td>
                            <td><?= Html::encode($item->quantidade) ?></td>
                            <td><?= Html::encode($item->unidade) ?></td>
                            <td>
                                <?= isset($categorias[$categoriaId])
                                        ? Html::encode($categorias[$categoriaId]->nome)
                                        : '—' ?>
                            </td>

                            <td>
                                <?php $validade = $item->validade; ?>

                                <?php if ($validade === null): ?>
                                    <span class="text-muted">Sem validade</span>
                                <?php else: ?>
                                    <?= Html::encode($validade) ?>

                                    <?php if ($validade < $hoje): ?>
                                        <span class="badge bg-danger ms-1">Expirado</span>
                                    <?php elseif ($validade === $hoje): ?>
                                        <span class="badge ms-1"
                                              style="background-color: #fd7e14; color: #fff;">Expira hoje</span>
                                    <?php elseif ($validade <= $limiteAviso): ?>
                                        <span class="badge bg-warning text-dark ms-1">Expira em breve</span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>

                            <td class="text-end text-nowrap">
                                <!-- Ver detalhes -->
                                <?= Html::a(
                                        '<i class="bi bi-eye"></i>',
                                        ['/item/view', 'id' => $item->id],
                                        [
                                                'class' => 'btn btn-outline-primary btn-sm',
                                                'title' => 'Ver detalhes',
                                                'aria-label' => 'Ver detalhes de ' . $item->nome,
                                        ]
                                ) ?>

                                <!-- Editar item -->
                                <?= Html::a(
                                        '<i class="bi bi-pencil-square"></i>',
                                        ['/item/update', 'id' => $item->id],
                                        [
                                                'class' => 'btn btn-outline-warning btn-sm',
                                                'title' => 'Editar item',
                                                'aria-label' => 'Editar ' . $item->nome,
                                        ]
                                ) ?>

                                <!-- Eliminar item -->
                                <?= Html::beginForm(
                                        ['/item/delete', 'id' => $item->id],
                                        'post',
                                        ['class' => 'd-inline']
                                ) ?>

                                <?= Html::submitButton(
                                        '<i class="bi bi-trash"></i>',
                                        [
                                                'class' => 'btn btn-outline-danger btn-sm',
                                                'title' => 'Eliminar item',
                                                'aria-label' => 'Eliminar ' . $item->nome,
                                                'data-confirm' =>
                                                        'Tens a certeza que queres eliminar este item? '
                                                        . 'O histórico de quantidades também será eliminado.',
                                        ]
                                ) ?>

                                <?= Html::endForm() ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div id="mensagem-sem-resultados"
                 class="alert alert-light border text-center mt-3 d-none"
                 role="status">
                Nenhum produto encontrado com os filtros selecionados.
            </div>

        <?php endif; ?>

    <?php endif; ?>
</div>

<?php
$this->registerJs(<<<'JS'
(function () {
    const pesquisa = document.getElementById('pesquisa-produtos');
    const filtroCategoria = document.getElementById('filtro-categoria');
    const itens = Array.from(document.querySelectorAll('.js-item-despensa'));
    const titulos = Array.from(document.querySelectorAll('.js-titulo-categoria'));
    const mensagem = document.getElementById('mensagem-sem-resultados');

    if (!pesquisa || !filtroCategoria || !mensagem) {
        return;
    }

    function normalizar(texto) {
        return String(texto || '')
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLocaleLowerCase('pt-PT')
            .trim();
    }

    function aplicarFiltros() {
        const texto = normalizar(pesquisa.value);
        const categoriaSelecionada = filtroCategoria.value;
        let visiveis = 0;

        itens.forEach(function (item) {
            const nome = normalizar(item.dataset.nome);
            const correspondeNome = nome.startsWith(texto);
            const correspondeCategoria = categoriaSelecionada === '' ||
                item.dataset.categoria === categoriaSelecionada;
            const mostrar = correspondeNome && correspondeCategoria;

            item.classList.toggle('d-none', !mostrar);
            if (mostrar) {
                visiveis++;
            }
        });

        titulos.forEach(function (titulo) {
            const idCategoria = titulo.dataset.categoria;
            const temItens = itens.some(function (item) {
                return item.dataset.categoria === idCategoria &&
                    !item.classList.contains('d-none');
            });
            titulo.classList.toggle('d-none', !temItens);
        });

        mensagem.classList.toggle('d-none', visiveis !== 0);
    }

    pesquisa.addEventListener('input', aplicarFiltros);
    filtroCategoria.addEventListener('change', aplicarFiltros);
    aplicarFiltros();
})();
JS, View::POS_READY);
?>