<?php

namespace App\Models;

use CodeIgniter\Model;

class ReferenciaCodeudorModel extends Model
{
    protected $table      = 'referencias_codeudor';
    protected $primaryKey = 'id_refe_codeudor';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'nombre',
        'parentesco',
        'direccion',
        'telefono',
        'id_codeudor'
    ];
    public function buscarPorCodeudor($id_codeudor)
    {
        return $this->where('id_codeudor', $id_codeudor)->findAll();
    }
}
