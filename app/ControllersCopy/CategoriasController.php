<?php

namespace App\Controllers;

use App\Models\CategoriasModel;

class CategoriasController extends BaseController
{
    private $nameClass = "CategoriasController";

    public function index()
    {
        $session = session();
        if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
            $url = $this->request->uri->getSegment(1);
            log_message("info", $this->nameClass . " " . $url);
            $accesos = json_decode($_SESSION['accesos'], true);
            $allowedUrls = array_column($accesos, 'url_acceso');
            if (in_array($url, $allowedUrls)) {
                $content4 = view('categorias/categorias');
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

    public function nuevaCategoria()
    {
        try {
            $session = session();
            if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
                $categoriasModel = new CategoriasModel();

                $nombre = $this->request->getPost('nombre');

                $data = [
                    'nombre' => $nombre
                ];
                if ($categoriasModel->insert($data)) {
                    echo json_encode(['success' => "Categoria creada exitosamente."]);
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


    public function getCategoriasAll()
    {
        try {
            $session = session();
            if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
                $categoriasModel = new CategoriasModel();
                $dataRsp = $categoriasModel->getCategorias();
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
    public function getCategoriasAllActivas()
    {
        try {
            $session = session();
            if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
                $categoriasModel = new CategoriasModel();
                $dataRsp = $categoriasModel->getCategorias();
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

    public function updateCategoria()
    {
        try {
            $session = session();
            if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
                $categoriasModel = new CategoriasModel();

                $nombre = $this->request->getPost('nombre');
                $estado = $this->request->getPost('estado');
                $id_categoria = $this->request->getPost('id_categoria');

                $data = [
                    'nombre' => $nombre,
                    'estado' => $estado
                ];

                log_message("info", "Valor que llegan del js:: " . json_encode($data));
                if ($categoriasModel->actualizarDataCategoria($data, $id_categoria)) {
                    echo json_encode(['success' => "Categoria actualizada exitosamente."]);
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
}
