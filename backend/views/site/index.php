<?php

/** @var yii\web\View $this */

$this->title = 'Dashboard';

?>

<div class="site-index">

    <h1 class="mt-2">
        Dashboard
    </h1>

    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">
            Painel Administrativo
        </li>
    </ol>

    <div class="row">

        <!-- Utilizadores -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card bg-primary text-white h-100">

                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-users me-2"></i>
                        Utilizadores
                    </h5>

                    <p class="card-text">
                        Gestão de contas e estados
                        dos utilizadores.
                    </p>
                </div>

                <div class="card-footer">
                    <small>Funcionalidade em desenvolvimento</small>
                </div>

            </div>
        </div>

        <!-- Categorias -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card bg-success text-white h-100">

                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-tags me-2"></i>
                        Categorias
                    </h5>

                    <p class="card-text">
                        Gestão das categorias
                        dos produtos.
                    </p>
                </div>

                <div class="card-footer">
                    <small>Funcionalidade em desenvolvimento</small>
                </div>

            </div>
        </div>

        <!-- Estatísticas -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card bg-warning h-100">

                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-chart-bar me-2"></i>
                        Estatísticas
                    </h5>

                    <p class="card-text">
                        Consulta de indicadores
                        do SmartLiving.
                    </p>
                </div>

                <div class="card-footer">
                    <small>Funcionalidade em desenvolvimento</small>
                </div>

            </div>
        </div>

    </div>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-home me-1"></i>
            SmartLiving
        </div>

        <div class="card-body">
            Bem-vindo ao painel administrativo
            do SmartLiving.

            <p class="mb-0 mt-2">
                Utiliza o menu lateral para aceder
                às funcionalidades de administração,
                à medida que forem implementadas.
            </p>
        </div>
    </div>

</div>
