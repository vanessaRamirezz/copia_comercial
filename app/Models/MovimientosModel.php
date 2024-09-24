<?php

namespace App\Models;

use CodeIgniter\Model;

class MovimientosModel extends Model
{
    protected $table = 'movimientos';
    protected $primaryKey = 'id_movimiento';
    protected $allowedFields = ['id_producto', 'id_tipo_movimiento','cantidad','fecha','descripcion','id_solicitud', 'id_documento'];

    public function getMovimientos()
    {
        return $this->findAll();
    }

    public function insertMovimiento($data)
    {
        return $this->insert($data);
    }

    public function getDescripcionById($id)
    {
        $result = $this->select('descripcion')->where('id_movimiento', $id)->first();
        return $result ? $result['descripcion'] : null;
    }
}
