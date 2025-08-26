<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProfesionModel;

class ProfesionController extends BaseController
{
    private $nameClass = "ProfesionController";
    public function index()
    {
        try {
            $session = session();

            if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
                $url = $this->request->uri->getSegment(1);
                log_message("info", $this->nameClass . " " . $url);
                $accesos = json_decode($_SESSION['accesos'], true);
                $allowedUrls = array_column($accesos, 'url_acceso');
                if (in_array($url, $allowedUrls)) {
                    log_message("info", $this->nameClass . " Acceso permitido");

                    $content4 = view('profesiones/profesion');
                    $fullPage = $this->renderPage($content4);
                    return $fullPage;
                } else {
                    $content4 = view('errors/html/error_403');
                    $fullPage = $this->renderPage($content4);
                    return $fullPage;
                }
            } else {
                return redirect()->to(base_url());
            }
        } catch (\Throwable $e) {
            $errorMessage = $this->nameClass . ' Ocurrió un error: ' . $e->getMessage() . PHP_EOL;
            $errorMessage .= ' Trace: ' . $e->getTraceAsString();

            log_message('error', $this->nameClass . " " . $errorMessage);
        }
    }

    public function save(){
        try {
            $session = session();
            if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
                $profesionModel = new ProfesionModel();
                $profesion = $this->request->getPost('profesionOficio');

                $data = [
                    'descripcion' => $profesion
                ];
                if ($profesionModel->insert($data)) {
                    echo json_encode(['success' => true, 'msg'=>"Profesion creado exitosamente."]);
                } else {
                    echo json_encode(['success' => false, 'msg'=>"Al parecer ocurrio un error al crear la profesion."]);
                }
            } else {
                return redirect()->to(base_url());
            }
        } catch (\Throwable $e) {
            $errorMessage = $this->nameClass . ' Ocurrió un error: ' . $e->getMessage() . PHP_EOL;
            $errorMessage .= ' Trace: ' . $e->getTraceAsString();

            log_message('error', $this->nameClass . " " . $errorMessage);
        }
    }

    public function obtenerProfesiones()
    {
        $model = new ProfesionModel();
        $profesiones = $model->findAll(); // Obtiene todas las profesiones

        return $this->response->setJSON($profesiones);
    }
}
