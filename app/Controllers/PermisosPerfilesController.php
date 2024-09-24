<?php

namespace App\Controllers;

use App\Models\PerfilesModel;

class PermisosPerfilesController extends BaseController
{
    private $perfilesModel;

    public function __construct()
    {
        $this->perfilesModel = new PerfilesModel();
    }
    public function index()
    {
        $session = session();
        if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
            $url = $this->request->uri->getSegment(1);
            $accesos = json_decode($_SESSION['accesos'], true);
            $allowedUrls = array_column($accesos, 'url_acceso');
            if (in_array($url, $allowedUrls)) {
                $content4 = view('permisos/permisosPerfiles');
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

    public function getperfiles()
    {
        try {
            $session = session();
            if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
                $dataRspPerfiles = $this->perfilesModel->findAll();  // Obtiene todos los perfiles

                // Recorre cada perfil para obtener sus accesos
                foreach ($dataRspPerfiles as &$perfil) {
                    $idPerfil = $perfil['id_perfil'];  // Obtiene el id_perfil actual
                    $dataAccesos = $this->perfilesModel->getAccesosXPerfiles($idPerfil);  // Obtiene los accesos para el perfil actual
                    $perfil['accesos'] = $dataAccesos;  // Añade los accesos al array de perfil
                }

                echo json_encode(['success' => true, "data" => $dataRspPerfiles]);  // Devuelve todos los perfiles con sus accesos
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

    public function getAccesos()
    {
        try {
            $session = session();
            if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
                $dataAccesos = $this->perfilesModel->accesosAll();  // Obtiene todos los perfiles
                echo json_encode(['success' => true, "data" => $dataAccesos]);  // Devuelve todos los perfiles con sus accesos
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

    public function asignarPermisos() {
        try {
            $session = session();
            if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
                $idPerfil = $this->request->getPost('idPerfil');
                $nPermisos = $this->request->getPost('accesos');
                $dataAccesos = $this->perfilesModel->getAccesosXPerfiles($idPerfil);
    
                $currentPermissions = array_column($dataAccesos, 'id_acceso');
    
                $toInsert = array_diff($nPermisos, $currentPermissions);
    
                $toDelete = array_diff($currentPermissions, $nPermisos);
    
                foreach ($toInsert as $idAcceso) {
                    $res= $this->perfilesModel->insertAccesoPerfil($idPerfil, $idAcceso);
                    log_message("info","El valor". $res);
                }
    
                foreach ($toDelete as $idAcceso) {
                    $this->perfilesModel->deleteAccesoPerfil($idPerfil, $idAcceso);
                }
    
                log_message("info", "Permisos insertados: " . print_r($toInsert, true));
                log_message("info", "Permisos eliminados: " . print_r($toDelete, true));
    
                echo json_encode(['success' => true]);
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
