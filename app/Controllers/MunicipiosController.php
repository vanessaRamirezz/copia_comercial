<?php

namespace App\Controllers;

use App\Models\MunicipiosModel;

class MunicipiosController extends BaseController
{
    private $nameClass = "MunicipiosController";

    public function index()
    {
        $session = session();
        if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
            $departamentoId = $this->request->getPost('departamento_id'); // Obtener el id del departamento del formulario

            if (!empty($departamentoId)) {
                $municipiosModel = new MunicipiosModel();
                $municipios = $municipiosModel->getMunicipiosByDepartamento($departamentoId);
                
                // Convertir los datos de los municipios a formato JSON y retornarlos
                return json_encode($municipios);
            } else {
                // Manejar el caso en que el id del departamento esté vacío
                return json_encode([]);
            }
        } else {
            return redirect()->to(base_url());
        }
    }
}
