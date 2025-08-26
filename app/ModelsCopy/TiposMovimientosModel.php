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


    public function getDescripcionByIdMov($id){
    return $this->asArray()
                ->select('descripcion, tipo_mov')
                ->where('id_tipo_movimiento', $id)
                ->first(); // ← Devuelve solo una fila como array
}

}
