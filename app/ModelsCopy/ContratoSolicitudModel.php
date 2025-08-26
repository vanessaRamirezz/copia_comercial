<?php

namespace App\Models;

use CodeIgniter\Model;

class ContratoSolicitudModel extends Model
{
    protected $table      = 'contrato_solicitud';           // Nombre de la tabla
    protected $primaryKey = 'id_contrato_solicitud';        // Clave primaria

    protected $allowedFields    = ['num_contrato', 'dir_contrato', 'fecha_creacion','id_sucursal','numero_solicitud','id_solicitud'];


    public function existeContratoPorSolicitud($id_solicitud)
    {
        return $this->where('id_solicitud', $id_solicitud)->first() !== null;
    }
}
