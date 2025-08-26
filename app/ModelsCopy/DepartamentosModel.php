<?php

namespace App\Models;
use CodeIgniter\Model;

class DepartamentosModel extends Model
{
    protected $table = 'departamentos';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id', 'nombre', 'cabecera', 'extension', 'ISO3166_2'];

    public function getDepartamentos()
    {
        return $this->findAll();
    }

    public function getDepartamentoPorCodigo($codigo)
    {
        // Ajusta la consulta según la estructura de tu tabla
        return $this->where('id', $codigo)->first()['nombre'];
    }
}
