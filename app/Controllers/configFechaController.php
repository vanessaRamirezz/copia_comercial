<?php

namespace App\Controllers;

use App\Models\ConfigFechaModel;

class configFechaController extends BaseController
{
    protected $fechaModel;

    public function __construct()
    {
        $this->fechaModel = new ConfigFechaModel();
    }

    public function index()
    {
        $session = session();
        if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
            $url = $this->request->uri->getSegment(1);
            $accesos = json_decode($_SESSION['accesos'], true);
            $allowedUrls = array_column($accesos, 'url_acceso');
            if (in_array($url, $allowedUrls)) {
                $fechaModel = new ConfigFechaModel();
                $fechaActiva = $fechaModel->obtenerActivosXSucursal($_SESSION['sucursal']);

                $fechaVirtual = null;
                if (!empty($fechaActiva)) {
                    $fechaVirtual = $fechaActiva[0]['fecha_virtual'];  // tomar solo la fecha virtual del primer registro
                }

                $data = [
                    'fechaVirtual' => $fechaVirtual
                ];

                log_message('info', 'El valor de la fecha virtual es: ' . $fechaVirtual);
                $content4 = view('configuracion_fecha/config', $data);
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

    public function getFechaActiva()
    {
        $session = session();
        $idSucursal = $_SESSION['sucursal'];
        // Buscar fecha activa para la sucursal
        $fechaActiva = $this->fechaModel
            ->where('estado', 'ACTIVO')
            ->where('id_sucursal', $idSucursal)
            ->orderBy('id', 'DESC')
            ->first();

        // Si no encuentra, buscar el último activo con id_sucursal = 0
        if (!$fechaActiva) {
            $fechaActiva = $this->fechaModel
                ->where('estado', 'ACTIVO')
                ->where('id_sucursal', 0)
                ->orderBy('id', 'DESC')
                ->first();
        }

        // Respuesta JSON
        if ($fechaActiva) {
            return $this->response->setJSON([
                'status' => 'ok',
                'data'   => $fechaActiva
            ]);
        } else {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'No hay fecha activa.'
            ]);
        }
    }


    public function guardarFechaConfig()
    {
        $session = session();
        $fecha = $this->request->getPost('fecha_virtual');
        $idSucursal = $_SESSION['sucursal']; // Obtener sucursal actual

        if (!$fecha) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'La fecha es requerida.'
            ]);
        }

        try {
            // Desactivar solo las fechas activas de la sucursal actual
            $this->fechaModel
                ->where('estado', 'ACTIVO')
                ->where('id_sucursal', $idSucursal)
                ->set('estado', 'INACTIVO')
                ->update();

            // Insertar la nueva fecha con estado ACTIVO para la sucursal actual
            $this->fechaModel->insert([
                'fecha_virtual' => $fecha,
                'estado' => 'ACTIVO',
                'id_sucursal' => $idSucursal
            ]);

            return $this->response->setJSON([
                'status' => 'ok',
                'message' => 'Fecha guardada correctamente.'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Ocurrió un error al guardar la fecha: ' . $e->getMessage()
            ]);
        }
    }
}
