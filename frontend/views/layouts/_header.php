<?php

use yii\helpers\Html;

?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container px-5">

        <?= Html::a(
                'SmartLiving',
                ['/site/index'],
                ['class' => 'navbar-brand fw-bold']
        ) ?>

        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarSmartLiving"
                aria-controls="navbarSmartLiving"
                aria-expanded="false"
                aria-label="Abrir menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse"
             id="navbarSmartLiving">

            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">

                <li class="nav-item">
                    <?= Html::a(
                            'Início',
                            ['/site/index'],
                            ['class' => 'nav-link']
                    ) ?>
                </li>

                <?php if (Yii::$app->user->isGuest): ?>

                    <li class="nav-item">
                        <?= Html::a(
                                'Entrar',
                                ['/site/login'],
                                ['class' => 'nav-link']
                        ) ?>
                    </li>

                    <li class="nav-item">
                        <?= Html::a(
                                'Registar',
                                ['/site/signup'],
                                ['class' => 'nav-link']
                        ) ?>
                    </li>

                <?php else: ?>

                    <!-- Menus visuais da Fase 1 -->
                    <li class="nav-item">
                        <span class="nav-link">
                            Minha Despensa
                        </span>
                    </li>

                    <li class="nav-item">
                        <span class="nav-link">
                            Agregado Familiar
                        </span>
                    </li>

                    <li class="nav-item">
                        <span class="nav-link">
                            Histórico
                        </span>
                    </li>

                    <li class="nav-item">
                        <span class="nav-link">
                            Perfil
                        </span>
                    </li>

                    <li class="nav-item ms-lg-3">
                        <?= Html::beginForm(
                                ['/site/logout'],
                                'post',
                                ['class' => 'd-flex']
                        ) ?>

                        <?= Html::submitButton(
                                'Sair (' .
                                Html::encode(
                                        Yii::$app->user->identity->username
                                ) . ')',
                                ['class' => 'btn btn-outline-light btn-sm']
                        ) ?>

                        <?= Html::endForm() ?>
                    </li>

                <?php endif; ?>

            </ul>
        </div>
    </div>
</nav>
