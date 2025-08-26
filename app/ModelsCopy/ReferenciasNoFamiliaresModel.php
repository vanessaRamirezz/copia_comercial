<?php

namespace App\Models;

use CodeIgniter\Model;

class ReferenciasNoFamiliaresModel extends Model
{
    protected $table      = 'referencias_no_familiares';
    protected $primaryKey = 'id_referencia_no_familiar';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'id_solicitud',
        'nombre',
        'direccion',
        'telefono',
        'lugar_trabajo',
        'telefono_trabajo'
    ];

     /**
     * Busca referencias no familiares por id_solicitud.
     *
     * @param int $id_solicitud
     * @return array
     */
    public function buscarPorSolicitud($id_solicitud)
    {
        // Construir la consulta para buscar por id_solicitud
        return $this->where('id_solicitud', $id_solicitud)->findAll();
    }
}
