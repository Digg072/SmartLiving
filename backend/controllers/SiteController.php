<?php

declare(strict_types=1);

namespace backend\controllers;

use backend\models\AdminLoginForm;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ErrorAction;
use yii\web\Response;
use common\models\User;
use yii\db\Query;
use common\models\AgregadoFamiliar;
use common\models\ItemDespensa;
use common\models\Categoria;
use yii\db\Expression;

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
        $totalUtilizadores = User::find()
            ->where(['<>', 'status', User::STATUS_DELETED])
            ->count();

        $totalAgregados = AgregadoFamiliar::find()->count();

        $totalItens = ItemDespensa::find()->count();

        // Contar itens por categoria através do ActiveRecord.
        $contagens = ItemDespensa::find()
            ->select([
                'categoria_id',
                'total' => new Expression('COUNT(*)'),
            ])
            ->groupBy('categoria_id')
            ->asArray()
            ->all();

        $totaisPorCategoria = [];

        foreach ($contagens as $contagem) {
            $totaisPorCategoria[(int) $contagem['categoria_id']] =
                (int) $contagem['total'];
        }

        // Obter todas as categorias, incluindo as que não têm itens.
        $categorias = Categoria::find()->all();

        $estatisticasCategorias = [];

        foreach ($categorias as $categoria) {
            $estatisticasCategorias[] = [
                'categoria' => $categoria->nome,
                'total' => $totaisPorCategoria[(int) $categoria->id] ?? 0,
            ];
        }

        // Ordenar pela quantidade de itens, do maior para o menor.
        usort($estatisticasCategorias, function ($a, $b) {
            return ($b['total'] <=> $a['total'])
                ?: strcasecmp($a['categoria'], $b['categoria']);
        });

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

        $model = new AdminLoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->loginAsAdministrator()) {
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
