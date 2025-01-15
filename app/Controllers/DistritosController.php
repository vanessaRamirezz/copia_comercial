<?php

namespace App\Controllers;

use App\Models\DistritosModel;

class DistritosController extends BaseController
{
    private $nameClass = "DistritosController";

    public function index()
    {
        $session = session();
        if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
            $id_municipio = $this->request->getPost('municipio_id'); // Obtener el id del departamento del formulario

            if (!empty($id_municipio)) {
                $distrito_model = new DistritosModel();
                $distritos = $distrito_model->getDistritosByMunicipio($id_municipio);
                return json_encode($distritos);
            } else {
                return json_encode([]);
            }
        } else {
            return redirect()->to(base_url());
        }
    }
}
