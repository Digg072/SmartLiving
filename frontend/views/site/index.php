<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'SmartLiving';

//Descrições das funcionalidades
$funcionalidades = [
        [
                'icone' => 'bi-box-seam',
                'titulo' => 'Gestão da Despensa',
                'descricao' => 'Organiza os produtos, as quantidades e as categorias da tua despensa.',
        ],
        [
                'icone' => 'bi-calendar-check',
                'titulo' => 'Controlo de Validades',
                'descricao' => 'Identifica os produtos próximos do prazo de validade e evita desperdícios.',
        ],
        [
                'icone' => 'bi-people',
                'titulo' => 'Agregado Familiar',
                'descricao' => 'Partilha a gestão da despensa com os membros do teu agregado familiar.',
        ],
];
?>

<div class="home-page">

    <div class="container">

        <!-- SECÇÃO PRINCIPAL -->

        <section class="home-hero">

            <div class="row align-items-center g-5">

                <!-- TEXTO -->

                <div class="col-lg-6">

                    <span class="home-label">
                        GESTÃO INTELIGENTE DA DESPENSA
                    </span>

                    <h1 class="home-title">
                        A tua despensa,
                        <span>sempre organizada.</span>
                    </h1>

                    <p class="home-description">
                        Organiza os teus alimentos, controla as quantidades
                        e acompanha os prazos de validade.
                        Tudo num só lugar, de forma simples e prática.
                    </p>

                    <div class="d-flex flex-wrap gap-3 mt-4">

                        <?php if (Yii::$app->user->isGuest): ?>

                            <?= Html::a(
                                    'Iniciar sessão',
                                    ['/site/login'],
                                    ['class' => 'btn btn-success home-button']
                            ) ?>

                            <?= Html::a(
                                    'Criar conta',
                                    ['/site/signup'],
                                    ['class' => 'btn btn-outline-success home-button']
                            ) ?>

                        <?php else: ?>

                            <?= Html::a(
                                    'A minha despensa',
                                    ['/despensa/index'],
                                    ['class' => 'btn btn-success home-button']
                            ) ?>

                            <?= Html::a(
                                    'Agregado familiar',
                                    ['/agregado/index'],
                                    ['class' => 'btn btn-outline-success home-button']
                            ) ?>

                        <?php endif; ?>

                    </div>

                </div>

                <!-- IMAGEM PRINCIPAL -->

                <div class="col-lg-6">

                    <div class="home-image-wrapper">

                        <?= Html::img(
                                Url::to('@web/images/hero-despensa.jpg'),
                                [
                                        'class' => 'home-main-image',
                                        'alt' => 'Organização de alimentos numa despensa',
                                ]
                        ) ?>

                    </div>

                </div>

            </div>

        </section>


        <!-- TRÊS FUNCIONALIDADES -->

        <section class="home-features">

            <div class="row g-4">

                <?php foreach ($funcionalidades as $funcionalidade): ?>

                    <div class="col-md-4">

                        <div class="home-feature">

                            <div class="home-feature-icon">

                                <i class="bi <?= Html::encode(
                                        $funcionalidade['icone']
                                ) ?>"></i>

                            </div>

                            <h3>
                                <?= Html::encode(
                                        $funcionalidade['titulo']
                                ) ?>
                            </h3>

                            <p>
                                <?= Html::encode(
                                        $funcionalidade['descricao']
                                ) ?>
                            </p>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        </section>

    </div>

</div>