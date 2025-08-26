<?php

namespace App\Controllers;

use App\Models\ClientesModel;
use App\Models\SucursalesModel;
use App\Models\SolicitudModel;
use App\Models\DisponibilidadProductosModel;

class RpExistenciaProductosController extends BaseController
{
    private $nameClass = "RpExistenciaProductosController";

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

                $content4 = view('reportes/rpExistenciaProductos', $data);
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

    public function getDataSucursales()
    {
        $dataSucursales = new SucursalesModel();
        $sucursales = $dataSucursales->getSucursalesAll();

        return $sucursales;
    }

    public function existenciaProductos()
    {
        try {
            $session = session();
            if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
                $sucursal_origen = $this->request->getPost('sucursal');

                log_message("info", 'origen suc ' . $sucursal_origen);

                $disponibilidadProductos = new DisponibilidadProductosModel();
                $resultDisponible = $disponibilidadProductos->getDisponibilidadProductosSucursal($sucursal_origen);

                /* $resultTotalSalidas = $disponibilidadProductos->getTotalSalidasPorSolicitud($sucursal_origen);
                log_message("info", "resultDisponible:: " . print_r($resultDisponible, true));
                log_message("info", "resultTotalSalidas:: " . print_r($resultTotalSalidas, true));

                $ajusteDisponibilidad = $this->nuevaDisponibilidad($resultDisponible, $resultTotalSalidas); */

                
                $productos = $resultDisponible;
                echo json_encode(['success' => $productos]);
                
            } else {
                return redirect()->to(base_url());
            }
        } catch (\Throwable $e) {
            log_message('error', $e->getMessage());
            echo json_encode(['error' => "Ocurrió un error en la base de datos."]);
        }
    }

    public function nuevaDisponibilidad($resultDisponible, $resultTotalSalidas)
    {
        // Log del array resultDisponible
        log_message("info", "Datos resultDisponible:: " . print_r($resultDisponible, true));
        log_message("info", "Cantidad de elementos en resultDisponible:: " . count($resultDisponible));

        // Log del array resultTotalSalidas
        log_message("info", "Datos resultTotalSalidas:: " . print_r($resultTotalSalidas, true));
        log_message("info", "Cantidad de elementos en resultTotalSalidas:: " . count($resultTotalSalidas));

        $totalSalidas = 0;
        foreach ($resultTotalSalidas as $salida) {
            $totalSalidas += $salida->totalSalida;
        }

        foreach ($resultDisponible as &$disponible) {
            $disponible->disponibilidad -= $totalSalidas;
        }
        return $resultDisponible;
    }
}
