<?php

namespace App\Controllers;

use App\Models\ClientesModel;
use App\Models\SucursalesModel;
use App\Models\MovimientosModel;

class KardexController extends BaseController
{
    private $nameClass = "KardexController";

    public function index()
    {
        $session = session();
        if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
            $url = $this->request->uri->getSegment(1);
            log_message("info", $this->nameClass . " " . $url);
            $accesos = json_decode($_SESSION['accesos'], true);
            $allowedUrls = array_column($accesos, 'url_acceso');
            if (in_array($url, $allowedUrls)) {

                $dataSucursales = new SucursalesModel();
                $sucursales = $dataSucursales->getSucursalesAll();

                // Pasar los datos a la vista
                $data = [
                    'sucursales' => $sucursales
                ];

                $content4 = view('reportes/kardex', $data);
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

    public function obtenerKardex()
    {
        $session = session();

        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acceso no permitido']);
        }

        $codigoProducto = $this->request->getPost('codigo_producto');
        $idSucursal = $session->get('sucursal'); // desde la sesión

        if (!$codigoProducto || !$idSucursal) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Datos incompletos']);
        }

        $movimientosModel = new MovimientosModel();
        $resultado = $movimientosModel->getKardex($codigoProducto, $idSucursal);

        return $this->response->setJSON($resultado);
    }
}
