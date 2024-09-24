<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductoSolicitudModel extends Model
{
    protected $table = 'productos_solicitud'; // Nombre de la tabla
    protected $primaryKey = 'id_producto_solicitud'; // Clave primaria

    protected $allowedFields = ['id_producto', 'cantidad_producto', 'id_solicitud'];

    public function buscarPorSolicitud($id_solicitud)
    {
        // Construir la consulta para buscar por id_solicitud
        return $this->select('*')
        ->where('id_solicitud', $id_solicitud)
        ->join('productos', 'productos.id_producto = productos_solicitud.id_producto')
        ->findAll();
    }
}
