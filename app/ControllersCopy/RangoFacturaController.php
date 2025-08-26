<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProfesionModel;
use App\Models\RangoFacturasModel;

class RangoFacturaController extends BaseController
{
    private $nameClass = "RangoFacturaController";
    public function index()
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
    }

    public function save()
    {
        try {
            $session = session();

            // Validar que la sesión esté activa
            if (!isset($_SESSION['sesion_activa']) || $_SESSION['sesion_activa'] !== true) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Sesión no válida, inicie sesión nuevamente.'
                ]);
            }

            // Recibir datos del formulario
            $numeroInicio = $this->request->getPost('numeroInicio');
            $numeroFinal = $this->request->getPost('numeroFinal');
            $sucursal = $this->request->getPost('sucursal');
            $estado = 'Activo';
            $usuarioID = $this->request->getPost('usuarioID');

            // Validar que los datos no estén vacíos
            if (empty($numeroInicio) || empty($numeroFinal) || empty($sucursal) || empty($estado) || empty($usuarioID)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Todos los campos son obligatorios.'
                ]);
            }

            // Insertar en la base de datos
            $data = [
                'numero_inicio' => $numeroInicio,
                'numero_fin' => $numeroFinal,
                'id_sucursal' => $sucursal,
                'estado' => $estado,
                'id_usuario_creador' => $usuarioID,  // Aquí usamos el ID del usuario
                'fecha_creacion' => date('Y-m-d H:i:s'),
                'estado' => 'Activo'
            ];

            // Crear una instancia del modelo
            $rangoFacturasModel = new RangoFacturasModel();
            $insert = $rangoFacturasModel->insert($data);  // Insertar los datos

            if ($insert) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Rango de facturas guardado correctamente.'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No se pudo guardar el rango de facturas.'
                ]);
            }
        } catch (\Throwable $e) {
            log_message('error', 'Error en save(): ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Ocurrió un error al guardar los datos.'
            ]);
        }
    }

    public function getRangos()
    {
        $model = new RangoFacturasModel();
        $rangoFacturas = $model->getRangoFacturas();
        return $this->response->setJSON($rangoFacturas);
    }
}
