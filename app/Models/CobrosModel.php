<?php

namespace App\Models;

use CodeIgniter\Model;

class CobrosModel extends Model
{
    protected $table = 'cobros';  // Nombre de la tabla
    protected $primaryKey = 'id_cobro';  // Llave primaria

    // Campos permitidos para insert y update
    protected $allowedFields = [
        'id_solicitud',
        'numero_cuota',
        'monto_cuota',
        'descripcion',
        'estado',
        'fecha_vencimiento',
        'fecha_pago',
        'interesGenerado',
        'esPrima'
    ];

    public function getCobrosBySolicitud($id_solicitud)
    {
        return $this->where('id_solicitud', $id_solicitud)
            ->orderBy('numero_cuota', 'ASC')
            ->findAll();
    }

    public function getCobroById($id_cobro)
    {
        return $this->where('id_cobro', $id_cobro)->first();  // Devuelve el primer registro que coincide con el id_cobro
    }
}
