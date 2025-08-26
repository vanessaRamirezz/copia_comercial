<?php

namespace App\Models;

use CodeIgniter\Model;

class MunicipiosModel extends Model
{
    protected $table = 'municipios';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id', 'nombre', 'departamento_id'];

    public function getMunicipiosByDepartamento($departamentoId)
    {
        return $this->where('departamento_id', $departamentoId)->findAll();
    }

    public function getMunicipioPorCodigo($codigo)
    {
        return $this->where('id', $codigo)->first()['nombre'];
    }
}
