<?php

declare(strict_types=1);

namespace backend\controllers;

use common\models\LoginForm;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ErrorAction;
use yii\web\Response;
use common\models\User;
use yii\db\Query;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions(): array
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */

    public function actionIndex()
    {
        // Total de utilizadores não eliminados
        $totalUtilizadores = User::find()
            ->where(['<>', 'status', User::STATUS_DELETED])
            ->count();

        // Total de agregados familiares
        $totalAgregados = (new Query())
            ->from('agregados_familiares')
            ->count();

        // Total de itens registados nas despensas
        $totalItens = (new Query())
            ->from('itens_despensa')
            ->count();

        // Número de itens associados a cada categoria
        $estatisticasCategorias = (new Query())
            ->select([
                'categoria' => 'c.nome',
                'total' => 'COUNT(i.id)',
            ])
            ->from(['c' => 'categorias'])
            ->leftJoin(
                ['i' => 'itens_despensa'],
                'i.categoria_id = c.id'
            )
            ->groupBy(['c.id', 'c.nome'])
            ->orderBy(['total' => SORT_DESC])
            ->all();

        return $this->render('index', [
            'totalUtilizadores' => $totalUtilizadores,
            'totalAgregados' => $totalAgregados,
            'totalItens' => $totalItens,
            'estatisticasCategorias' => $estatisticasCategorias,
        ]);
    }

    /**
     * Login action.
     *
     * @return string|Response
     */
    public function actionLogin(): string|Response
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->layout = 'blank';

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout(): Response
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
