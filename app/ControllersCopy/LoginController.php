<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\LoginModel;

class LoginController extends BaseController
{
    private $encrypter;
    public function __construct()
    {
        $this->encrypter = \Config\Services::encrypter();
    }

    public function index()
    {
        $session = session();
        if (!isset($_SESSION['sesion_activa'])) {
            // Renderiza la primera vista y la guarda en $content4
            $content4 = view('login/login');
            // $fullPage = $this->renderPage($content4);

            // Combina el contenido de ambas vistas y devuelve la respuesta
            return $content4;
        } else {
            var_dump(isset($_SESSION) ? $_SESSION : 'No se ha iniciado la sesión, intenta usar session_start(); primero');
            return redirect()->to(base_url('perfil'));
        }
    }

    public function validateUser()
    {
        try {
            log_message('info', 'iniciando sesion');
            $loginModel = new LoginModel();
            $usuario = $this->request->getPost('user');
            $pass = $this->request->getPost('pwd');

            $datosUsuario =  $loginModel->validateUser(['usuario' => $usuario]);

            if (count($datosUsuario) > 0 && $pass === $this->encrypter->decrypt(base64_decode($datosUsuario[0]['contrasena']))) {
                $dataAccesos = $loginModel->getAccesosUsuario($datosUsuario[0]['dui']);
                log_message("info", "Valor retornado del acceso es:: " . json_encode($dataAccesos));
                $session = \Config\Services::session();
                $dataSession = [
                    'id_usuario' => $datosUsuario[0]['id_usuario'],
                    'nombres' => $datosUsuario[0]['nombres'],
                    'apellidos' => $datosUsuario[0]['apellidos'],
                    'id_perfil' => $datosUsuario[0]['id_perfil'],
                    'duiUsuario' => $datosUsuario[0]['dui'],
                    'sucursal' => $datosUsuario[0]['id_sucursal'],
                    'sesion_activa' => true,
                    'correo' => $datosUsuario[0]['correo'],
                    'telefono' => $datosUsuario[0]['telefono'],
                    'contrasena' => $datosUsuario[0]['contrasena'],
                    'sucursalN' => $datosUsuario[0]['sucursal'],
                    'perfilN' => $datosUsuario[0]['tipo_perfil'],
                    'accesos' => json_encode($dataAccesos)
                ];
                $session->set($dataSession);
                log_message('debug', 'Sesión actual: ' . print_r(session()->get(), true));

                // Obtener los url_acceso donde orden_acceso es igual a 1
                $url_acceso = array_column(array_filter($dataAccesos, function ($item) {
                    return $item['orden_acceso'] === '1';
                }), 'url_acceso');

                // Verificar si se encontró un URL de acceso con orden_acceso igual a 1
                if (!empty($url_acceso)) {
                    // Redireccionar a la primera URL encontrada
                    $redirect_url = $url_acceso[0];
                    echo json_encode([
                        'redirect' => base_url($redirect_url),
                        'sucursal' => $datosUsuario[0]['sucursal']
                    ]);
                } else {
                    // Si no se encontró ningún URL de acceso válido, puedes manejar esto según tus necesidades
                    echo json_encode(['error' => 'No se encontró un URL de acceso válido con orden_acceso igual a 1']);
                }
            } else {
                echo json_encode(['error' => 'Usuario o contraseña incorrectos']);
            }
        } catch (\Throwable $e) {
            $errorMessage = 'Ocurrió un error: ' . $e->getMessage() . PHP_EOL;
            $errorMessage .= 'Trace: ' . $e->getTraceAsString();

            log_message('error', $errorMessage);
            echo json_encode(['error' => 'Ocurrio un error al momento de la validación.']);
        }
    }

    public function cambiarSucursal()
    {
        $session = \Config\Services::session();
    
        // Verificar si el usuario está en sesión
        if (!$session->has('id_usuario')) {
            return $this->response->setJSON(['error' => 'Sesión no encontrada. Inicie sesión nuevamente.']);
        }
    
        $idSucursal = $this->request->getPost('sucursal');
        $nombreSucursal = $this->request->getPost('sucursalN');
    
        if (!$idSucursal || !$nombreSucursal) {
            return $this->response->setJSON(['error' => 'Debe seleccionar una sucursal válida.']);
        }
    
        // Actualizar la sesión con la nueva sucursal
        $session->set('sucursal', $idSucursal);
        $session->set('sucursalN', $nombreSucursal);
        log_message('debug', 'Sesión actual: ' . print_r(session()->get(), true));
    
        return $this->response->setJSON([
            'success' => 'Sucursal cambiada exitosamente.',
            'redirect' => base_url('solicitudes') // Redirige al dashboard o donde desees
        ]);
    }
    

    public function logout()
    {
        try {
            $session = session();
            $session->destroy();
            return redirect()->to(base_url('/'));
        } catch (\Throwable $e) {
            $errorMessage = 'Ocurrió un error: ' . $e->getMessage() . PHP_EOL;
            $errorMessage .= 'Trace: ' . $e->getTraceAsString();

            log_message('error', $errorMessage);
        }
    }


    public function recuperarPassword()
    {
        try {
            log_message('info', 'iniciando recuperar contraseña');
            $loginModel = new LoginModel();
            $duiRecuperar = $this->request->getPost('duiRecuperar');

            $datosUsuario =  $loginModel->validateUser(['usuario' => $duiRecuperar]);

            $token =  $loginModel->generateUniqueToken();
            $rspUpdate = $loginModel->updateToken($duiRecuperar, $token);

            log_message('info', 'El token generado es: '. $token.'-- y la respuesta es: '.$rspUpdate);

            if (count($datosUsuario) > 0 && $rspUpdate) {
                $email = \Config\Services::email();
                $email->setTo($datosUsuario[0]['correo']);
                $email->setFrom('palaciosjaime400@gmail.com', 'Creditos V1');
                $email->setSubject('Recuperación de Contraseña');
    
                $email->setMessage(view('correos/recuperarPassword', ['token' => $token]));

                if ($email->send()) {
                    log_message('info', 'Correo enviado con éxito');
                } else {
                    log_message('error', 'Error al enviar el correo: ' . $email->printDebugger());
                }
                echo json_encode(['success' => count($datosUsuario)]);
                
            } else {
                echo json_encode(['error' => 'El DUI ingresado no existe']);
            }
        } catch (\Throwable $e) {
            $errorMessage = 'Ocurrió un error: ' . $e->getMessage() . PHP_EOL;
            $errorMessage .= 'Trace: ' . $e->getTraceAsString();

            log_message('error', $errorMessage);
            echo json_encode(['error' => 'Ocurrio un error al momento de la validación.']);
        }
    }
}
