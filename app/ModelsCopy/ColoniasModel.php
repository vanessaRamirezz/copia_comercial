<?php

namespace App\Models;

use CodeIgniter\Model;

class ColoniasModel extends Model
{
    protected $table = 'colonias';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id', 'nombre', 'id_distrito'];

    public function getColoniasByDistrito($idMunicipio)
    {
        return $this->where('id_distrito', $idMunicipio)->findAll();
    }

    public function getColoniasByCliente($id_colonia)
    {
        return $this->where('id', $id_colonia)->first()['nombre'];        
    }
}
