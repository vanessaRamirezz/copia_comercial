<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MenuModel;

class MenuController extends BaseController
{    
    public function obtenerMenuPorUsuario()
    {
        $session = session();
        if (isset($_SESSION['sesion_activa'])) {
           $id_perfil = $_SESSION['id_perfil'];

           $menuModel = new MenuModel();
           $dataMenu = $menuModel->getMenu($id_perfil);
           return $dataMenu;
        } else {
            return redirect()->to(base_url());
        }
    }
}
