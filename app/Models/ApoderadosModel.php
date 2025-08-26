<?php

namespace App\Models;

use CodeIgniter\Model;

class ApoderadosModel extends Model
{
    protected $table = 'apoderados';
    protected $primaryKey = 'idapoderado';
    protected $allowedFields = ['nombre_apoderado', 'dui_apoderado', 'representante_legal', 'dui_representante', 'estado', 'fecha_nacimiento_apoderado', 'fecha_nacimiento_rLegal'];

    //si es 1 es porque esta deshabilitado, sino esta habilitado
    public function getApoderados()
    {
        return $this->where('estado !=', 0)->findAll();
    }

    public function getUltimoApoderadoCreado()
    {
        return $this->orderBy('idapoderado', 'DESC')->first();
    }
}
