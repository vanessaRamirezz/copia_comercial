<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AdministracionModel;

class AdministracionController extends BaseController
{

    private $encrypter;
    private $nameClass = "AdministracionController";
    public function __construct()
    {
        $this->encrypter = \Config\Services::encrypter();
    }
    public function index()
    {
        try {
            $session = session();

            if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
                $url = $this->request->uri->getSegment(1);
                log_message("info", $this->nameClass . " " . $url);
                $accesos = json_decode($_SESSION['accesos'], true);
                $allowedUrls = array_column($accesos, 'url_acceso');
                if (in_array($url, $allowedUrls)) {
                    log_message("info", $this->nameClass . " Acceso permitido");

                    $content4 = view('welcome_message');
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

    public function perfilUsuarioGeneral()
    {
        try {
            $session = session();
            if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
                $url = $this->request->uri->getSegment(1);
                log_message("info", $this->nameClass . " " . $url);
                $accesos = json_decode($_SESSION['accesos'], true);
                $allowedUrls = array_column($accesos, 'url_acceso');
                if (in_array($url, $allowedUrls)) {

                    $content4 = view('perfil/perfilUsuario');
                    $fullPage = $this->renderPage($content4);
                    // Combina el contenido de ambas vistas y devuelve la respuesta
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
            $errorMessage = 'Ocurrió un error: ' . $e->getMessage() . PHP_EOL;
            $errorMessage .= 'Trace: ' . $e->getTraceAsString();

            log_message('error', $errorMessage);
        }
    }

    public function actualizarContrasena()
    {
        try {
            $session = session();
            if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
                $url = $this->request->uri->getSegment(1);
                log_message("info", $this->nameClass . " " . $url);

                $pwdNueva = $this->request->getPost('pwdNueva');
                $pwdNuevaConfirma = base64_encode($this->encrypter->encrypt($this->request->getPost('pwdNuevaConfirma')));
                $pwdActual = $this->request->getPost('pwdActual');

                if ($pwdActual === $this->encrypter->decrypt(base64_decode($_SESSION['contrasena']))) {
                    $adminModel = new AdministracionModel();

                    if ($adminModel->updatePassword($_SESSION['dui'], $pwdNuevaConfirma)) {
                        $_SESSION['contrasena'] = $pwdNuevaConfirma;
                        echo json_encode(['success' => 'Contraseña actualizada con exito']);
                    } else {
                        echo json_encode(['error' => 'ocurrio un error al intentar actualizar la contraseña']);
                    }
                } else {
                    echo json_encode(['info' => 'La contraseña actual es incorrecta, intente nuevamente.']);
                }
            } else {
                return redirect()->to(base_url());
            }
        } catch (\Throwable $e) {
            $errorMessage = 'Ocurrió un error: ' . $e->getMessage() . PHP_EOL;
            $errorMessage .= 'Trace: ' . $e->getTraceAsString();

            log_message('error', $errorMessage);
        }
    }

    public function actualizarDatosUsuario()
    {
        try {
            $session = session();
            if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
                $url = $this->request->uri->getSegment(1);
                log_message("info", $this->nameClass . " " . $url);

                $nombres =  $this->request->getPost('nombresUsuario');
                $apellidos =  $this->request->getPost('apellidosUsuario');
                $correo =  $this->request->getPost('emailUsuario');
                $telefono =  $this->request->getPost('numTelefono');

                log_message('info', $nombres . ' ' . $apellidos . ' ' . $correo . ' ' . $telefono);
                $adminModel = new AdministracionModel();

                $data = [
                    'nombres' => $nombres,
                    'apellidos' => $apellidos,
                    'correo' => $correo,
                    'telefono' => $telefono
                ];
                if ($adminModel->updateInfoUsurio($data, $_SESSION['dui'])) {
                    $_SESSION['nombres'] = $nombres;
                    $_SESSION['apellidos'] = $apellidos;
                    $_SESSION['correo'] = $correo;
                    $_SESSION['telefono'] = $telefono;
                    echo json_encode(['success' => 'Datos actualizados correctamente']);
                } else {
                    echo json_encode(['error' => 'ocurrio un error al intentar actualizar los datos']);
                }
            } else {
                return redirect()->to(base_url());
            }
        } catch (\Throwable $e) {
            $errorMessage = 'Ocurrió un error: ' . $e->getMessage() . PHP_EOL;
            $errorMessage .= 'Trace: ' . $e->getTraceAsString();

            log_message('error', $errorMessage);
        }
    }
}
