<?php

namespace App\Controllers;

use App\Models\DepartamentosModel;

class DepartamentosController extends BaseController
{
    private $nameClass = "DepartamentosController";

    public function getAllDepartamentos()
    {
        try {
            $session = session();
            if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
                $departamentosModel = new DepartamentosModel();
                $departamentos = $departamentosModel->getDepartamentos();

                // Devolver los datos de los departamentos en formato JSON
                return $this->response->setJSON(['departamentos' => $departamentos]);
                
            } else {
                return redirect()->to(base_url());
            }
        } catch (\Throwable $th) {
            log_message('ERROR', 'ERROR Retorno de departamentos ' . $th);
            // Manejar el error y devolver una respuesta adecuada
            return $this->response->setJSON(['error' => 'Error al obtener los departamentos']);
        }
    }
}
