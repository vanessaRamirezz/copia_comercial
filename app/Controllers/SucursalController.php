<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SucursalesModel;

class SucursalController extends BaseController
{
    private $nameClass = "SucursalController";
    /* public function index()
    {
        try {
            $session = session();

            if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
                $url = $this->request->uri->getSegment(1);
                
                $accesos = json_decode($_SESSION['accesos'], true);
                $allowedUrls = array_column($accesos, 'url_acceso');
                if (in_array($url, $allowedUrls)) {
                    $content4 = view('rangos_facturas/rangoFactura');
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
    } */

    public function getSucursales()
    {
        $sucursalesModel = new SucursalesModel();
        $sucursales = $sucursalesModel->getSucursalesAll();
        return $this->response->setJSON($sucursales);
    }
}
