<?php

namespace App\Models;

use CodeIgniter\Model;

class RangoFacturasModel extends Model
{
    protected $table = 'rango_factura';
    protected $primaryKey = 'id_rango_factura';
    protected $allowedFields = ['id_rango_factura', 'numero_inicio', 'numero_fin', 'id_sucursal', 'id_usuario_creador', 'fecha_creacion','estado'];

    public function getRangoFacturas()
    {
        // Realizar la consulta con INNER JOIN entre rango_factura, sucursal y usuarios
        $builder = $this->db->table('rango_factura')
            ->select('rango_factura.*, sucursal.sucursal as nombre_sucursal, CONCAT(usuarios.nombres, " ", usuarios.apellidos) AS nombre_usuario, rango_factura.estado')
            ->join('sucursal', 'sucursal.id_sucursal = rango_factura.id_sucursal', 'inner')
            ->join('usuarios', 'usuarios.id_usuario = rango_factura.id_usuario_creador', 'inner');

        $query = $builder->get();
        return $query->getResult(); // Retorna todos los resultados de la consulta
    }
}

