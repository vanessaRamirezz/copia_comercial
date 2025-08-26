<?php

namespace App\Models;

use CodeIgniter\Model;

class DistritosModel extends Model
{
    protected $table = 'distritos';
    protected $primaryKey = 'id_distrito';
    protected $allowedFields = ['id_distrito', 'nombre', 'id_municipio'];

    public function getDistritosByMunicipio($id_municipio)
    {
        return $this->where('id_municipio', $id_municipio)->findAll();
    }
    public function getDistritosBId($id_distrito)
    {
        return $this->where('id_distrito', $id_distrito)->first()['nombre'];
    }
}
