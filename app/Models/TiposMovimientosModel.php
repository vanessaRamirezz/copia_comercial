<?php

namespace App\Models;

use CodeIgniter\Model;

class TiposMovimientosModel extends Model
{

    protected $table = 'tipos_movimiento';
    protected $primaryKey = 'id_tipo_movimiento';
    protected $allowedFields = ['descripcion', 'tipo_mov'];

    public function getDescripcionById($id){
        $this->select('*');
        $this->where('id_tipo_movimiento',$id);
        return $this->findAll();
    }
}
