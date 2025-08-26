<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use App\Models\UsuariosModel;

class EstadoUsuarioFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // Si no hay sesión activa → redirige a login
        if (!$session->has('id_usuario')) {
            return redirect()->to('/');
        }

        $id_usuario = $session->get('id_usuario');

        // Validar estado en la base de datos
        $usuarioModel = new UsuariosModel();
        $usuario = $usuarioModel->where('id_usuario', $id_usuario)->first();

        if (!$usuario || $usuario['activo'] !== 'SI') {
            // Si no existe o está desactivado, destruir sesión y redirigir
            $session->destroy();
            return redirect()->to('/')->with('error', 'Tu usuario ha sido desactivado.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No necesitamos nada después
    }
}
