<?php

namespace App\Models;

use CodeIgniter\Model;

class ProfesionModel extends Model
{
    protected $table      = 'profesiones';
    protected $primaryKey = 'id_profesion';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'descripcion'
    ];
}
