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
                $content4 = view('configuracion_fecha/config');
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
        $fechaActiva = $this->fechaModel
            ->where('estado', 'ACTIVO')
            ->orderBy('id', 'DESC')
            ->first();

        if ($fechaActiva) {
            return $this->response->setJSON([
                'status' => 'ok',
                'data' => $fechaActiva
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No hay fecha activa.'
            ]);
        }
    }

    public function guardarFechaConfig()
    {
        $fecha = $this->request->getPost('fecha_virtual');

        if (!$fecha) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'La fecha es requerida.'
            ]);
        }

        try {
            // Desactivar todas las fechas activas
            $this->fechaModel
                ->where('estado', 'ACTIVO')
                ->set('estado', 'INACTIVO')
                ->update();

            // Insertar la nueva fecha con estado ACTIVO
            $this->fechaModel->insert([
                'fecha_virtual' => $fecha,
                'estado' => 'ACTIVO'
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
