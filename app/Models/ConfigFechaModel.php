<?php

namespace App\Models;

use CodeIgniter\Model;

class ConfigFechaModel extends Model
{
    protected $table            = 'control_fecha_registro';
    protected $primaryKey       = 'id';

    protected $allowedFields    = [
        'fecha_virtual',
        'estado',
        'id_sucursal'
    ];


    public function obtenerActivos()
    {
        return $this->where('estado', 'ACTIVO')->findAll();
    }

    public function obtenerActivosXSucursal($idSucursal)
    {
        // Buscar activos para la sucursal
        $result = $this->where('estado', 'ACTIVO')
            ->where('id_sucursal', $idSucursal)
            ->findAll();

        if (!empty($result)) {
            return $result;
        }

        // Si no hay activos para la sucursal, devolver el último activo con id_sucursal = 0
        return $this->where('estado', 'ACTIVO')
            ->where('id_sucursal', 0)
            ->orderBy('id', 'DESC')
            ->findAll(1); // solo el último activo
    }
}
