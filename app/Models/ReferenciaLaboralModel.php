<?php

namespace App\Models;

use CodeIgniter\Model;

class ReferenciaLaboralModel extends Model
{
    protected $table = 'referencias_laborales';
    protected $primaryKey = 'id_referencia_laboral';

    protected $allowedFields = [
        'id_solicitud',
        'id_profesion',
        'empresa',
        'direccion_trabajo',
        'telefono_trabajo',
        'cargo',
        'salario',
        'tiempo_laborado_empresa',
        'nombre_jefe_inmediato',
        'empresa_anterior',
        'telefono_empresa_anterior'
    ];

    protected $useAutoIncrement = true;

    /**
     * Busca todas las referencias laborales asociadas a una solicitud específica.
     *
     * @param int $id_solicitud ID de la solicitud
     * @return array|null Referencias laborales encontradas, o null si no hay ninguna
     */
    public function obtenerReferenciasPorSolicitud(int $id_solicitud)
    {
        // Realizar la consulta para obtener las referencias laborales por id_solicitud
        $referencias = $this->select('referencias_laborales.*, profesiones.descripcion')
                            ->join('profesiones', 'referencias_laborales.id_profesion = profesiones.id_profesion', 'left')
                            ->where('id_solicitud', $id_solicitud)
                            ->findAll();

        // Devolver las referencias encontradas, o null si no se encontró ninguna
        return $referencias;
    }
}
