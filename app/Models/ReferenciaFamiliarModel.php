<?php

namespace App\Models;

use CodeIgniter\Model;

class ReferenciaFamiliarModel extends Model
{
    protected $table = 'referencias_familiares'; // Nombre de la tabla en la base de datos
    protected $primaryKey = 'id_referencia_familiar'; // Llave primaria de la tabla

    protected $allowedFields = [
        'id_solicitud',
        'nombre',
        'parentesco',
        'direccion',
        'telefono',
        'lugar_trabajo',
        'telefono_trabajo'
    ];

     /**
     * Busca referencias familiares por id_solicitud.
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
