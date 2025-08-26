<?php

namespace App\Models;

use CodeIgniter\Model;

class ReferenciasCrediticiasModel extends Model
{
    protected $table      = 'referencias_crediticias';
    protected $primaryKey = 'id_referencia_crediticia';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'id_solicitud',
        'institucion',
        'telefono',
        'monto_credito',
        'periodos',
        'plazo',
        'estado'
    ];

    public function buscarPorSolicitud($id_solicitud)
    {
        // Construir la consulta para buscar por id_solicitud
        return $this->where('id_solicitud', $id_solicitud)->findAll();
    }
}
