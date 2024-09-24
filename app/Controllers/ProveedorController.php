<?php

namespace App\Controllers;

use App\Models\ProveedoresModel;

class ProveedorController extends BaseController
{
    private $nameClass = "ProveedorController";

    public function index()
    {
        $session = session();
        if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
            $url = $this->request->uri->getSegment(1);
            log_message("info", $this->nameClass . " " . $url);
            $accesos = json_decode($_SESSION['accesos'], true);
            $allowedUrls = array_column($accesos, 'url_acceso');
            if (in_array($url, $allowedUrls)) {
                $content4 = view('proveedores/proveedores');
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

    public function nuevoProveedor()
    {
        try {
            $session = session();
            if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
                $ProveedoresModel = new ProveedoresModel();

                $nombre = $this->request->getPost('nombre');
                $contacto = $this->request->getPost('contacto');
                $telefono = $this->request->getPost('telefono');
                $email = $this->request->getPost('correo');
                $direccion = $this->request->getPost('direccion');

                $data = [
                    'nombre' => $nombre,
                    'contacto' => $contacto,
                    'telefono' => $telefono,
                    'email' => $email,
                    'direccion' => $direccion,
                ];
                if ($ProveedoresModel->insert($data)) {
                    echo json_encode(['success' => "Proveedor creado exitosamente."]);
                } else {
                    echo json_encode(['error' => "Al parecer ocurrio un error."]);
                }
            } else {
                return redirect()->to(base_url());
            }
        } catch (\Throwable $e) {
            // Otros errores de base de datos
            log_message('error', $e->getMessage());
            echo json_encode(['error' => "Ocurrió un error en la base de datos."]);
        }
    }

    public function updateProveedor()
    {
        try {
            $session = session();
            if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
                $ProveedoresModel = new ProveedoresModel();

                $nombre = $this->request->getPost('nombre');
                $contacto = $this->request->getPost('contacto');
                $telefono = $this->request->getPost('telefono');
                $email = $this->request->getPost('correo');
                $direccion = $this->request->getPost('direccion');
                $estado = $this->request->getPost('estado');
                $id_proveedor = $this->request->getPost('id_proveedor');

                $data = [
                    'nombre' => $nombre,
                    'contacto' => $contacto,
                    'telefono' => $telefono,
                    'email' => $email,
                    'estado' => $estado,
                    'direccion' => $direccion,
                ];
                if ($ProveedoresModel->actualizarDataProveedor($data, $id_proveedor)) {
                    echo json_encode(['success' => "Proveedor actualizado exitosamente."]);
                } else {
                    echo json_encode(['error' => "Al parecer ocurrio un error."]);
                }
            } else {
                return redirect()->to(base_url());
            }
        } catch (\Throwable $e) {
            // Otros errores de base de datos
            log_message('error', $e->getMessage());
            echo json_encode(['error' => "Ocurrió un error en la base de datos."]);
        }
    }


    public function getProveedoresAll()
    {
        try {
            $session = session();
            if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
                $ProveedoresModel = new ProveedoresModel();
                $dataRsp = $ProveedoresModel->getProveedores();
                echo json_encode(['success' => $dataRsp]);
            } else {
                return redirect()->to(base_url());
            }
        } catch (\Throwable $e) {
            $errorMessage = 'Ocurrió un error: ' . $e->getMessage() . PHP_EOL;
            $errorMessage .= 'Trace: ' . $e->getTraceAsString();

            log_message('error', $errorMessage);
            echo json_encode(['error' => "Al parecer hay un error al recuperar los datos."]);
        }
    }

    public function getProveedoresAllActives()
    {
        try {
            $session = session();
            if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
                $ProveedoresModel = new ProveedoresModel();
                $dataRsp = $ProveedoresModel->getProveedoresActivos();
                return $dataRsp;
            } else {
                return redirect()->to(base_url());
            }
        } catch (\Throwable $e) {
            $errorMessage = 'Ocurrió un error: ' . $e->getMessage() . PHP_EOL;
            $errorMessage .= 'Trace: ' . $e->getTraceAsString();

            log_message('error', $errorMessage);
            echo json_encode(['error' => "Al parecer hay un error al recuperar los datos."]);
        }
    }
}
