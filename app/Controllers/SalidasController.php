<?php

namespace App\Controllers;

use App\Models\DocumentosModel;
use App\Models\MovimientosModel;
use App\Models\SucursalesModel;
use App\Models\TiposMovimientosModel;
use Dompdf\Dompdf;
use TCPDF;
use App\Models\ConfigFechaModel;

class SalidasController extends BaseController
{
    private $nameClass = "SalidasController";
    private $proveedoresController;
    private $sucursalesModel;
    private $tiposMovimientosModel;

    public function __construct()
    {
        $this->proveedoresController = new ProveedorController();
        $this->sucursalesModel = new SucursalesModel();
        $this->tiposMovimientosModel = new TiposMovimientosModel();
    }

    public function index()
    {
        $session = session();
        if ($session->has('sesion_activa') && $session->get('sesion_activa') === true) {
            $url = $this->request->uri->getSegment(1);
            log_message("info", $this->nameClass . " " . $url);
            $accesos = json_decode($session->get('accesos'), true);
            $allowedUrls = array_column($accesos, 'url_acceso');
            if (in_array($url, $allowedUrls)) {
                $proveedoresActivos = $this->proveedoresController->getProveedoresAllActives();
                $sucursales = $this->sucursalesModel->getSucursalesAll();
                $tiposMovimientos = $this->tiposMovimientosModel->findAll();

                $fechaModel = new ConfigFechaModel();
                $fechaActiva = $fechaModel->obtenerActivosXSucursal($session->get('sucursal'));

                $fechaVirtual = null;
                if (!empty($fechaActiva)) {
                    $fechaVirtual = $fechaActiva[0]['fecha_virtual'];  // tomar solo la fecha virtual del primer registro
                }

                log_message('info', 'El valor de la fecha virtual es: ' . $fechaVirtual);

                $data = [
                    'proveedoresActivos' => $proveedoresActivos,
                    'sucursales' => $sucursales,
                    'tiposMovimientos' => $tiposMovimientos,
                    'tiposMovimientosM' => $tiposMovimientos,
                    'fechaVirtual' => $fechaVirtual
                ];

                $content4 = view('movimientos/formato_salida', $data);
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
}
