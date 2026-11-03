<?php

use yii\helpers\Html;

/** @var yii\web\View $this */

$this->title = 'SmartLiving';

?>

<div class="site-index">

    <!-- Hero da Modern Business -->
    <header class="bg-dark py-5 rounded-3">
        <div class="container px-5">
            <div class="row gx-5 justify-content-center">

                <div class="col-lg-10">
                    <div class="text-center my-5">

                        <h1 class="display-5 fw-bolder text-white mb-3">
                            Bem-vindo ao SmartLiving
                        </h1>

                        <p class="lead fw-normal text-white-50 mb-4">
                            Gere os produtos da tua despensa,
                            acompanha as quantidades e
                            controla as datas de validade.
                        </p>

                        <?php if (Yii::$app->user->isGuest): ?>

                            <div class="d-grid gap-3 d-sm-flex
                                        justify-content-sm-center">

                                <?= Html::a(
                                        'Começar agora',
                                        ['/site/signup'],
                                        ['class' => 'btn btn-primary btn-lg px-4']
                                ) ?>

                                <?= Html::a(
                                        'Entrar',
                                        ['/site/login'],
                                        ['class' => 'btn btn-outline-light btn-lg px-4']
                                ) ?>

                            </div>

                        <?php else: ?>

                            <p class="text-white">
                                Olá,
                                <?= Html::encode(
                                        Yii::$app->user->identity->username
                                ) ?>!
                            </p>

                        <?php endif; ?>

                    </div>
                </div>

            </div>
        </div>
    </header>

    <!-- Funcionalidades -->
    <section class="py-5" id="funcionalidades">

        <div class="container px-4">
            <h2 class="fw-bolder mb-5 text-center">
                O que podes fazer no SmartLiving?
            </h2>

            <div class="row gx-5">

                <div class="col-lg-4 mb-5">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body p-4">

                            <h3 class="h5 fw-bold">
                                Gestão da Despensa
                            </h3>

                            <p class="text-muted mb-0">
                                Organiza os produtos, as
                                quantidades e as categorias
                                da tua despensa.
                            </p>

                        </div>
                    </div>
                </div>

                <div class="col-lg-4 mb-5">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body p-4">

                            <h3 class="h5 fw-bold">
                                Controlo de Validades
                            </h3>

                            <p class="text-muted mb-0">
                                Identifica os produtos
                                próximos do prazo de validade
                                e evita desperdícios.
                            </p>

                        </div>
                    </div>
                </div>

                <div class="col-lg-4 mb-5">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body p-4">

                            <h3 class="h5 fw-bold">
                                Agregado Familiar
                            </h3>

                            <p class="text-muted mb-0">
                                Partilha a gestão da despensa
                                com os membros do teu
                                agregado familiar.
                            </p>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

</div>
