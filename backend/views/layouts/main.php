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

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
<?php $this->beginBody() ?>

<div class="app-wrapper">
    <nav class="app-header navbar navbar-expand bg-body">
        <div class="container-fluid">
            <button type="button"
                    class="btn btn-outline-secondary"
                    data-lte-toggle="sidebar"
                    aria-label="Mostrar ou ocultar o menu lateral">
                ☰
            </button>

            <span class="ms-3 fw-semibold">Back-Office</span>

            <?php if (!Yii::$app->user->isGuest): ?>
                <?= Html::beginForm(['/site/logout'], 'post', ['class' => 'ms-auto']) ?>
                <?= Html::submitButton('Sair', ['class' => 'btn btn-outline-secondary btn-sm']) ?>
                <?= Html::endForm() ?>
            <?php endif; ?>
        </div>
    </nav>

    <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
        <div class="sidebar-brand">
            <?= Html::a('SmartLiving', ['/site/index'], ['class' => 'brand-link']) ?>
        </div>

        <div class="sidebar-wrapper">
            <nav class="mt-2" aria-label="Menu do Back-Office">
                <?= \yii\widgets\Menu::widget([
                        'options' => ['class' => 'nav sidebar-menu flex-column'],
                        'itemOptions' => ['class' => 'nav-item'],
                        'linkTemplate' => '<a href="{url}" class="nav-link"><p>{label}</p></a>',
                        'items' => [
                                ['label' => 'Dashboard', 'url' => ['/site/index']],
                                [
                                        'label' => 'Utilizadores',
                                        'url' => ['/user/index'],
                                        'visible' => Yii::$app->user->can('consultarUtilizadores'),
                                ],
                                [
                                        'label' => 'Categorias',
                                        'url' => ['/categoria/index'],
                                        'visible' => Yii::$app->user->can('consultarCategorias'),
                                ],
                        ],
                ]) ?>
            </nav>
        </div>
    </aside>

    <main class="app-main">
        <div class="app-content py-4">
            <div class="container-fluid">
                <?= \common\widgets\Alert::widget() ?>
                <?= $content ?>
            </div>
        </div>
    </main>

    <footer class="app-footer">
        SmartLiving — Gestão da Despensa de Casa
    </footer>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
