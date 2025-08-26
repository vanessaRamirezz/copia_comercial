<?php

namespace App\Controllers;

use App\Models\ClientesModel;
use App\Models\SucursalesModel;

class RpMoraClientesController extends BaseController
{
    private $nameClass = "RpMoraClientesController";

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
                $content4 = view('reportes/rpMoraCliente', $data);
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

    public function getData()
    {
        try {
            // Obtener los filtros desde el request
            $departamento = $this->request->getPost('departamento');
            $municipio = $this->request->getPost('municipio');
            $distrito = $this->request->getPost('distrito');
            $colonia = $this->request->getPost('colonia');
            $sucursal = $this->request->getPost('sucursal');

            $clienteModel = new ClientesModel();

            // Filtros que se enviarán al modelo
            $filtros = [
                'departamento' => $departamento,
                'municipio' => $municipio,
                'distrito' => $distrito,
                'colonia' => $colonia,
                'sucursal' => $sucursal
            ];

            // Obtener resultados del modelo
            $resultados = $clienteModel->obtenerMoraClientes($filtros);
            log_message("info", "El valor que retorna cliente es: " . json_encode($resultados));

            // Inicializar acumuladores
            $total_sin_vencer = 0;
            $total_d1_30 = 0;
            $total_d31_60 = 0;
            $total_d61_90 = 0;
            $total_d91_120 = 0;
            $total_d121_150 = 0;
            $total_d_mas_150 = 0;
            $total_general = 0;

            // Procesar los resultados para el formato requerido
            $datos = [];
            foreach ($resultados as $resultado) {
                // Sumar totales
                $total_sin_vencer += $resultado->sin_vencer;
                $total_d1_30 += $resultado->mora_1_30;
                $total_d31_60 += $resultado->mora_31_60;
                $total_d61_90 += $resultado->mora_61_90;
                $total_d91_120 += $resultado->mora_91_120;
                $total_d121_150 += $resultado->mora_121_150;
                $total_d_mas_150 += $resultado->mora_mas_150;
                $total_general += $resultado->total_mora;

                $datos[] = [
                    'codigo' => $resultado->numero_solicitud,
                    'nombre' => $resultado->cliente,
                    'sin_vencer' => number_format($resultado->sin_vencer, 2, '.', ''),
                    'd1_30' => number_format($resultado->mora_1_30, 2, '.', ''),
                    'd31_60' => number_format($resultado->mora_31_60, 2, '.', ''),
                    'd61_90' => number_format($resultado->mora_61_90, 2, '.', ''),
                    'd91_120' => number_format($resultado->mora_91_120, 2, '.', ''),
                    'd121_150' => number_format($resultado->mora_121_150, 2, '.', ''),
                    'd_mas_150' => number_format($resultado->mora_mas_150, 2, '.', ''),
                    'total' => number_format($resultado->total_mora, 2, '.', ''),
                    'contrato' => str_pad($resultado->numero_solicitud, 6, '0', STR_PAD_LEFT),
                    'fecha_compra' => date('d/m/Y', strtotime($resultado->fecha_compra)),
                    'fecha_ultimo_pago' => '',
                    'por' => '',
                    'telefono' => $resultado->telefono
                ];
            }

            // Agregar la fila de totales al final
            $datos[] = [
                'total' => 'Totales',
                'sin_vencer' => number_format($total_sin_vencer, 2, '.', ''),
                'd1_30' => number_format($total_d1_30, 2, '.', ''),
                'd31_60' => number_format($total_d31_60, 2, '.', ''),
                'd61_90' => number_format($total_d61_90, 2, '.', ''),
                'd91_120' => number_format($total_d91_120, 2, '.', ''),
                'd121_150' => number_format($total_d121_150, 2, '.', ''),
                'd_mas_150' => number_format($total_d_mas_150, 2, '.', ''),
                'total_general' => number_format($total_general, 2, '.', '')
            ];

            return $this->response->setJSON($datos);
        } catch (\Throwable $th) {
            log_message("error", "ERROR AL PROCESAR DATOS DE MORA: " . $th);
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Ocurrió un error en el servidor']);
        }
    }
}
