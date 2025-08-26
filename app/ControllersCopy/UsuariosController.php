<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PerfilesModel;
use App\Models\SucursalesModel;
use App\Models\UsuariosModel;

class UsuariosController extends BaseController
{
    private $encrypter;
    public function __construct()
    {
        $this->encrypter = \Config\Services::encrypter();
    }
    public function index()
    {
        $session = session();
        if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
            $sucursalesModel = new SucursalesModel();
            $perfilesModel = new PerfilesModel();

            $data['sucursales'] = $sucursalesModel->getSucursales($_SESSION['sucursal']);
            $data['perfiles'] = $perfilesModel->getPerfiles($_SESSION['id_perfil']);
            log_message('info', json_encode($data));

            $content4 = view('usuarios/usuarios', $data);
            $fullPage = $this->renderPage($content4);
            return $fullPage;
        } else {
            return redirect()->to(base_url());
        }
    }

    public function getUsuariosAll()
    {
        try {
            $session = session();
            if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
                $UsuariosModel = new UsuariosModel();
                $dataRsp = $UsuariosModel->getUsuarioXSucursal($_SESSION['sucursal'], $_SESSION['duiUsuario']);
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

    public function nuevoUsuario()
    {
        try {
            $session = session();
            if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
                $UsuariosModel = new UsuariosModel();

                $nombres = $this->request->getPost('nombres');
                $apellidos = $this->request->getPost('apellidos');
                $correo = $this->request->getPost('correo');
                $dui = $this->request->getPost('duiNew');
                $telefono = $this->request->getPost('telefono');
                $perfil = $this->request->getPost('id_perfil');
                $pwd = $this->request->getPost('password');
                $sucursal = $_SESSION['sucursal'];
                $contrasena = base64_encode($this->encrypter->encrypt($pwd));

                $data = [
                    'nombres' => $nombres,
                    'apellidos' => $apellidos,
                    'dui' => $dui,
                    'correo' => $correo,
                    'telefono' => $telefono,
                    'contrasena' => $contrasena,
                    'id_perfil' => $perfil,
                    'id_sucursal' => $sucursal
                ];
                if ($UsuariosModel->insert($data)) {
                    echo json_encode(['success' => "Usuario creado exitosamente."]);
                } else {
                    echo json_encode(['error' => "Al parecer ocurrio un error."]);
                }
            } else {
                return redirect()->to(base_url());
            }
        } catch (\Throwable $e) {
            if ($e->getCode() == 1062) { // Código de error para valor duplicado
                echo json_encode(['error' => "El valor del campo dui ya existe en la tabla."]);
            } else {
                // Otros errores de base de datos
                log_message('error', $e->getMessage());
                echo json_encode(['error' => "Ocurrió un error en la base de datos."]);
            }
        }
    }

    public function actulizarInfoUsuario()
    {
        try {
            $url = $this->request->uri->getSegment(1);
            log_message("info", $url);
            $session = session();
            if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
                $UsuariosModel = new UsuariosModel();

                $nombres = $this->request->getPost('nombres');
                $apellidos = $this->request->getPost('apellidos');
                $correo = $this->request->getPost('correo');
                $dui = $this->request->getPost('duiNew');
                $telefono = $this->request->getPost('telefono');
                $perfil = $this->request->getPost('perfil');
                $sucursal = $_SESSION['sucursal'];

                $data = [
                    'nombres' => $nombres,
                    'apellidos' => $apellidos,
                    'correo' => $correo,
                    'telefono' => $telefono,
                    'id_perfil' => $perfil,
                    'id_sucursal' => $sucursal
                ];
                if ($UsuariosModel->actualizarDataUsuario($data, $dui)) {
                    echo json_encode(['success' => "Usuario actualizado exitosamente."]);
                } else {
                    echo json_encode(['error' => "Al parecer ocurrio un error."]);
                }
            } else {
                return redirect()->to(base_url());
            }
        } catch (\Throwable $e) {
            log_message('error', $e->getMessage());
            echo json_encode(['error' => "Ocurrió un error en la base de datos."]);
        }
    }

    public function deleteUsuario()
    {
        try {
            $UsuariosModel = new UsuariosModel();

            $dui = $this->request->getPost('duiNew');
            if ($UsuariosModel->eliminarUsuarioPorDui($dui)) {
                echo json_encode(['success' => "Usuario eliminado exitosamente."]);
            } else {
                echo json_encode(['error' => "Al parecer ocurrio un error."]);
            }
        } catch (\Throwable $e) {
            log_message('error', $e->getMessage());
            echo json_encode(['error' => "Ocurrió un error en la base de datos."]);
        }
    }

    public function recuperarPwdAct()
    {
        try {
            log_message("info", "Actualizar password por recuperacion");
            $dui = $this->request->getPost('dui');
            $contrasena = base64_encode($this->encrypter->encrypt($this->request->getPost('contrasena')));
            $token = $this->request->getPost('token');
            $UsuariosModel = new UsuariosModel();

            $usuario = $UsuariosModel->getUsuarioPorDui($dui);
            log_message("debug", "data usuario:: ".print_r($usuario, true));
            log_message("info", "token usuario: " . $usuario['token'] . ' token post: ' . $token);
            if ($usuario['token'] == $token) {
                log_message("info", "el token es igual: " . $token);
                $data = [
                    'token' => '',
                    'contrasena' => $contrasena
                ];
                // Actualizar la contraseña y resetear el token
                $rspUpdate = $UsuariosModel->updatePWD($dui, $data);

                log_message("info", "la respuesta de actualizar es:: ".$rspUpdate);
                if ($rspUpdate == 1) {
                    echo json_encode(['success' => "Contraseña actualizada correctamente."]);
                }else {
                    echo json_encode(['error' => "Error al actualizar la contraseña."]);
                }
                
            } else {
                echo json_encode(['error' => "El token no es correcto, ingrese el token correcto."]);
            }
        } catch (\Throwable $e) {
            log_message('error', $e);
            echo json_encode(['error' => "Ocurrió un error en la base de datos."]);
        }
    }
}
