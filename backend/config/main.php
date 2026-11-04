<?php

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    'as access' => [
        'class' => \yii\filters\AccessControl::class,
        'rules' => [
            // Login e página de erro acessíveis a todos
            [
                'allow' => true,
                'controllers' => ['site'],
                'actions' => ['login', 'error'],
            ],

            // Qualquer utilizador autenticado pode terminar sessão
            [
                'allow' => true,
                'controllers' => ['site'],
                'actions' => ['logout'],
                'roles' => ['@'],
            ],

            // Apenas administradores podem aceder ao Back-Office
            [
                'allow' => true,
                'roles' => ['administrador'],
            ],
        ],
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'user' => [
            'identityClass' => \common\models\User::class,
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        */
        'assetManager' => [
            'bundles' => [
                \yii\bootstrap5\BootstrapAsset::class => [
                    'css' => [],
                ],
            ],
        ],
    ],
    'params' => $params,
];
