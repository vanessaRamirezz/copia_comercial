<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SucursalesModel;

class SucursalController extends BaseController
{
    private $nameClass = "SucursalController";
    public function index()
    {
        try {
            $session = session();

            if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
                $url = $this->request->uri->getSegment(1);

                $accesos = json_decode($_SESSION['accesos'], true);
                $allowedUrls = array_column($accesos, 'url_acceso');
                if (in_array($url, $allowedUrls)) {
                    $content4 = view('sucursales/sucursales');
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

    public function getSucursales()
    {
        $sucursalesModel = new SucursalesModel();
        $sucursales = $sucursalesModel->getSucursalesAll();
        return $this->response->setJSON($sucursales);
    }

    public function getSucursalesDescripcion()
    {
        $sucursalesModel = new SucursalesModel();
        $sucursales = $sucursalesModel->getSucursalesDescripcion();
        return $this->response->setJSON($sucursales);
    }

    public function save()
    {
        try {
            $request = $this->request;

            $idsucursal = trim($request->getPost('idsucursal'));
            $sucursal = trim($request->getPost('sucursal'));
            $id_departamento = $request->getPost('depto');
            $id_municipio = $request->getPost('muni');
            $id_distrito = $request->getPost('distrito');
            $id_colonia = $request->getPost('colonia');

            // Validación simple del lado servidor (por seguridad)
            if (!$sucursal || $id_departamento == -1 || $id_municipio == -1 || $id_distrito == -1 || $id_colonia == -1) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Todos los campos son obligatorios.'
                ]);
            }

            $sucursalesModel = new SucursalesModel();

            $data = [
                'sucursal' => $sucursal,
                'id_departamento' => $id_departamento,
                'id_municipio' => $id_municipio,
                'id_distrito' => $id_distrito,
                'id_colonia' => $id_colonia,
            ];

            if (empty($idsucursal)) {
                // Insertar nuevo registro
                $sucursalesModel->insert($data);
                $message = 'Sucursal guardada correctamente';
            } else {
                // Actualizar registro existente
                $sucursalesModel->update($idsucursal, $data);
                $message = 'Sucursal actualizada correctamente';
            }

            return $this->response->setJSON([
                'status' => 'ok',
                'message' => $message
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'SucursalController::save - ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error interno al guardar la sucursal'
            ]);
        }
    }
}
