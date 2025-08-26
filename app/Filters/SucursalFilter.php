<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Config\Database;

class SucursalFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $sucursal = $session->get('sucursal') ?? 0;

        $db = Database::connect();
        $db->simpleQuery("SET @id_sucursal_usuario = {$sucursal}");
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No es necesario hacer nada aquí
    }
}
