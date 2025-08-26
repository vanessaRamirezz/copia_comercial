<?php

namespace App\Controllers;

use App\Models\ClientesModel;
use App\Models\SucursalesModel;
use App\Models\SolicitudModel;

class RpClientesController extends BaseController
{
    private $nameClass = "RpClientesController";

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

                $content4 = view('reportes/reporteClientes', $data);
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

    public function getDataRpClientes()
    {
        $departamento = $this->request->getPost('departamento');
        $municipio = $this->request->getPost('municipio');
        $distrito = $this->request->getPost('distrito');
        $colonia = $this->request->getPost('colonia');
        $sucursal = $this->request->getPost('sucursal');
        $estado = $this->request->getPost('estado');

        $clienteModel = new ClientesModel();
        $solicitudModel = new SolicitudModel();

        $filtros = [
            'departamento' => $departamento,
            'municipio' => $municipio,
            'distrito' => $distrito,
            'colonia' => $colonia,
            'sucursal' => $sucursal,
            'estado' => $estado
        ];

        $clientes = $clienteModel->obtenerClientesFiltrados($filtros);

        $clientesConSolicitudes = [];
        $totalGeneral = 0; // 🟡 Inicializa el total general

        foreach ($clientes as $cliente) {
            $solicitudes = $solicitudModel->obtenerSolicitudesPorCliente($cliente['id_cliente']);

            $montoTotal = 0;

            foreach ($solicitudes as &$solicitud) {
                $montoTotal += (float)$solicitud['montoApagar'];
            }

            // 🟢 Suma al total general
            $totalGeneral += $montoTotal;

            $clientesConSolicitudes[] = [
                'dui' => $cliente['dui'],
                'id_cliente' => $cliente['id_cliente'],
                'nombre_cliente' => $cliente['nombre_completo'],
                'solicitudes' => $solicitudes,
                'total_monto' => $montoTotal
            ];
        }

        log_message("info", "El valor que retorna cliente es: " . json_encode($clientesConSolicitudes));

        return $this->response->setJSON([
            'status' => 'ok',
            'data' => $clientesConSolicitudes,
            'total_general' => $totalGeneral // 🔵 Retorna también el total general
        ]);
    }
}
