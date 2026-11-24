<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;

class RbacController extends Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->authManager;

        $roles = [
            'utilizador' => [
                'consultarDespensa',
                'criarItem',
                'consultarItens',
                'editarItem',
                'eliminarItem',
                'consultarHistoricoStock',
                'consultarCategorias',
                'criarAgregado',
                'consultarAgregado',
                'gerirProprioPerfil',
                'sairAgregado',
            ],
            'gestorAgregado' => [
                'editarAgregado',
                'eliminarAgregado',
                'adicionarMembroAgregado',
                'removerMembroAgregado',
            ],
            'administrador' => [
                'criarCategoria',
                'editarCategoria',
                'eliminarCategoria',
                'consultarCategorias',
                'criarUtilizador',
                'editarUtilizador',
                'eliminarUtilizador',
                'consultarUtilizadores',
                'consultarEstatisticas',
            ],
        ];

        foreach ($roles as $roleName => $permissions) {
            $role = $auth->getRole($roleName);

            if ($role === null) {
                $role = $auth->createRole($roleName);
                $auth->add($role);
            }

            foreach ($permissions as $permissionName) {
                $permission = $auth->getPermission($permissionName);

                if ($permission === null) {
                    $permission = $auth->createPermission($permissionName);
                    $auth->add($permission);
                }

                if (!$auth->hasChild($role, $permission)) {
                    $auth->addChild($role, $permission);
                }
            }
        }

        // Gestor de agregado herda todas as permissões do utilizador.
        $gestor = $auth->getRole('gestorAgregado');
        $utilizador = $auth->getRole('utilizador');

        if (!$auth->hasChild($gestor, $utilizador)) {
            $auth->addChild($gestor, $utilizador);
        }

        echo "Roles e permissions criadas com sucesso.\n";
    }
}
