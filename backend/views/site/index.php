
<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var int|string $totalUtilizadores */
/** @var int|string $totalAgregados */
/** @var int|string $totalItens */
/** @var array $estatisticasCategorias */

$this->title = 'Dashboard';

?>

<div class="site-index">

    <h1 class="mt-2">Dashboard</h1>

    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">
            Estatísticas Gerais do SmartLiving
        </li>
    </ol>

    <!-- Cartões de estatísticas -->
    <div class="row">

        <!-- Utilizadores -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">

                    <h5>
                        <i class="fas fa-users me-2"></i>
                        Utilizadores
                    </h5>

                    <h2 class="display-6 fw-bold">
                        <?= (int) $totalUtilizadores ?>
                    </h2>

                    <small>
                        Utilizadores registados
                    </small>

                </div>
            </div>
        </div>

        <!-- Agregados Familiares -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card bg-success text-white h-100">
                <div class="card-body">

                    <h5>
                        <i class="fas fa-home me-2"></i>
                        Agregados Familiares
                    </h5>

                    <h2 class="display-6 fw-bold">
                        <?= (int) $totalAgregados ?>
                    </h2>

                    <small>
                        Agregados existentes
                    </small>

                </div>
            </div>
        </div>

        <!-- Itens da Despensa -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card bg-warning h-100">
                <div class="card-body">

                    <h5>
                        <i class="fas fa-box me-2"></i>
                        Itens da Despensa
                    </h5>

                    <h2 class="display-6 fw-bold">
                        <?= (int) $totalItens ?>
                    </h2>

                    <small>
                        Itens registados no sistema
                    </small>

                </div>
            </div>
        </div>

    </div>

    <!-- Estatísticas das Categorias -->
    <div class="card mb-4">

        <div class="card-header">
            <i class="fas fa-chart-bar me-1"></i>
            Utilização das Categorias
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-striped table-hover">

                    <thead>
                    <tr>
                        <th>Categoria</th>
                        <th class="text-end">
                            Número de Itens
                        </th>
                    </tr>
                    </thead>

                    <tbody>

                    <?php foreach ($estatisticasCategorias as $categoria): ?>

                        <tr>
                            <td>
                                <?= Html::encode($categoria['categoria']) ?>
                            </td>

                            <td class="text-end">
                                <?= (int) $categoria['total'] ?>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                    <?php if (empty($estatisticasCategorias)): ?>

                        <tr>
                            <td colspan="2" class="text-center text-muted">
                                Ainda não existem categorias.
                            </td>
                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>
