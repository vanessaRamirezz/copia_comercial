<?php

namespace App\Controllers;

use App\Models\RpTransaccionesDiariasModel;

class RpTransaccionesDiariasController extends BaseController
{
    private $nameClass = "RpTransaccionesDiariasController";

    public function index()
    {
        $session = session();
        if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
            $url = $this->request->uri->getSegment(1);
            log_message("info", $this->nameClass . " " . $url);
            $accesos = json_decode($_SESSION['accesos'], true);
            $allowedUrls = array_column($accesos, 'url_acceso');
            if (in_array($url, $allowedUrls)) {
                $content4 = view('reportes/transaccionesDiarias');
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
    }

    public function getDataTransDiarias()
    {
        $fechaInicio = $this->request->getPost('fechaInicio');
        $fechaFin = $this->request->getPost('fechaFin');

        // Verificar si llegaron las fechas
        if (!$fechaInicio || !$fechaFin) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Debe proporcionar ambas fechas.'
            ]);
        }

        $rpTransDiarias = new RpTransaccionesDiariasModel();
        $rpTrans = $rpTransDiarias->getTransaccionesPorFecha($fechaInicio, $fechaFin);

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $rpTrans
        ]);
    }
}
