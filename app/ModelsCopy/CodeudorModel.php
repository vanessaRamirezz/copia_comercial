<?php

namespace App\Models;

use CodeIgniter\Model;

class CodeudorModel extends Model
{
    protected $table      = 'codeudor';
    protected $primaryKey = 'id_codeudor';

    protected $allowedFields = [
        'nombre',
        'dui',
        'direccion',
        'telefono_personal',
        'vive_en_casa_propia',
        'en_promesa_de_venta',
        'alquilada',
        'tiempo',
        'estado_civil',
        'nombre_conyugue',
        'profesion_oficio',
        'patrono_empresa',
        'direccion_trabajo',
        'telefono_trabajo',
        'cargo',
        'salario',
        'nombre_jefe_inmediato',
        'id_solicitud'
    ];

    public function buscarPorSolicitud($id_solicitud)
    {
        return $this->where('id_solicitud', $id_solicitud)->findAll();
    }
}
