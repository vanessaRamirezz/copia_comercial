<?php

namespace App\Controllers;

use App\Models\ColoniasModel;

class ColoniaController extends BaseController
{
    private $nameClass = "ColoniaController";

    public function index()
    {
        $session = session();
        if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
            $content4 = view('Colonia/colonia');
            $fullPage = $this->renderPage($content4);
            return $fullPage;
        } else {
            return redirect()->to(base_url());
        }
    }

    public function getColonias()
    {
        $session = session();
        if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
            $id_distrito = $this->request->getPost('id_distrito');

            if (!empty($id_distrito)) {
                $model = new ColoniasModel();
                $municipios = $model->getColoniasByDistrito($id_distrito);
                return json_encode($municipios);
            } else {
                return json_encode([]);
            }
        } else {
            return redirect()->to(base_url());
        }
    }

    public function saveColonia()
    {
        $session = session();
        if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
            $model = new ColoniasModel();
            $data = $this->request->getPost();
            if (empty($data['nombre']) || empty($data['id_distrito'])) {
                return $this->response->setJSON(['success' => false, 'msg' => 'El nombre y el ID del municipio son requeridos.']);
            }

            $coloniaData = [
                'nombre' => $data['nombre'],
                'id_distrito' => $data['id_distrito']
            ];
            if ($model->insert($coloniaData)) {
                return $this->response->setJSON(['success' => true, 'msg' => 'Colonia guardada exitosamente.']);
            } else {
                return $this->response->setJSON(['success' => false, 'msg' => 'Ocurrió un error al guardar la colonia.']);
            }
        }
    }

    public function getColoniaXDistrito()
    {
        $session = session();
        if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
            $municipioID = $this->request->getPost('municipio_id');
            log_message("info", $municipioID);

            if (!empty($municipioID)) {
                $model = new ColoniasModel();
                $colonia = $model->getColoniasByDistrito($municipioID);
                
                return json_encode($colonia);
            } else {
                return json_encode([]);
            }
        } else {
            return redirect()->to(base_url());
        }
    }

    public function getColoniaCliente()
    {
        $session = session();
        if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
            $idColoniaCliente = $this->request->getPost('idColonia');
            log_message("info", $idColoniaCliente);

            if (!empty($idColoniaCliente)) {
                $model = new ColoniasModel();
                $colonia = $model->getColoniasByCliente($idColoniaCliente);
                
                return json_encode($colonia);
            } else {
                return json_encode([]);
            }
        } else {
            return redirect()->to(base_url());
        }
    }
}
