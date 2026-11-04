<?php

use backend\assets\AppAsset;
use common\widgets\Alert;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var string $content */

AppAsset::register($this);

?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Html::encode(Yii::$app->language) ?>"
      data-bs-theme="light">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <?= Html::csrfMetaTags() ?>

    <title>
        <?= Html::encode($this->title) ?> - SmartLiving
    </title>

    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js"
            crossorigin="anonymous"></script>

    <?php $this->head() ?>
</head>

<body class="sb-nav-fixed">
<?php $this->beginBody() ?>

<!-- Barra superior -->
<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">

    <?= Html::a(
            'SmartLiving BO',
            ['/site/index'],
            ['class' => 'navbar-brand ps-3']
    ) ?>

    <button class="btn btn-link btn-sm order-1 order-lg-0
                   me-4 me-lg-0"
            id="sidebarToggle"
            type="button"
            aria-label="Mostrar ou ocultar menu">
        <i class="fas fa-bars"></i>
    </button>

    <div class="ms-auto me-4 d-flex align-items-center gap-3">

        <?php if (!Yii::$app->user->isGuest): ?>

            <span class="text-white small">
                <?= Html::encode(
                        Yii::$app->user->identity->username
                ) ?>
            </span>

            <?= Html::beginForm(['/site/logout'], 'post', [
                    'class' => 'm-0'
            ]) ?>

            <?= Html::submitButton('Sair', [
                    'class' => 'btn btn-outline-light btn-sm'
            ]) ?>

            <?= Html::endForm() ?>

        <?php endif; ?>

    </div>
</nav>

<div id="layoutSidenav">

    <!-- Menu lateral -->
    <div id="layoutSidenav_nav">

        <nav class="sb-sidenav accordion sb-sidenav-dark"
             id="sidenavAccordion">

            <div class="sb-sidenav-menu">
                <div class="nav">

                    <div class="sb-sidenav-menu-heading">
                        Principal
                    </div>

                    <?= Html::a(
                            '<div class="sb-nav-link-icon">
                            <i class="fas fa-tachometer-alt"></i>
                        </div>Dashboard',
                            ['/site/index'],
                            ['class' => 'nav-link']
                    ) ?>

                    <div class="sb-sidenav-menu-heading">
                        Administração
                    </div>

                    <!-- Menus apenas visuais nesta fase -->
                    <?= Html::a(
                            '<span class="sb-nav-link-icon">
                                 <i class="fas fa-users"></i>
                            </span>
                            Gestão de Utilizadores',
                            ['/user/index'],
                            ['class' => 'nav-link']
                    ) ?>

                    <span class="nav-link text-white-50">
                        <span class="sb-nav-link-icon">
                            <i class="fas fa-tags"></i>
                        </span>
                        Gestão de Categorias
                    </span>

                    <span class="nav-link text-white-50">
                        <span class="sb-nav-link-icon">
                            <i class="fas fa-chart-bar"></i>
                        </span>
                        Estatísticas
                    </span>

                </div>
            </div>

            <div class="sb-sidenav-footer">
                <div class="small">SmartLiving</div>
                Painel Administrativo
            </div>

        </nav>
    </div>

    <!-- Conteúdo principal -->
    <div id="layoutSidenav_content">

        <main>
            <div class="container-fluid px-4 py-4">

                <?= Alert::widget() ?>

                <?= $content ?>

            </div>
        </main>

        <footer class="py-4 bg-light mt-auto">
            <div class="container-fluid px-4">

                <div class="d-flex align-items-center
                            justify-content-between small">

                    <div class="text-muted">
                        Copyright &copy; SmartLiving
                        <?= date('Y') ?>
                    </div>

                    <div class="text-muted">
                        Back-Office
                    </div>

                </div>

            </div>
        </footer>

    </div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
