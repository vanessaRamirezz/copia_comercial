<?php

namespace App\Models;

use CodeIgniter\Model;

class PlanDePagoModel extends Model
{
    protected $table = 'plan_de_pago'; // Nombre de la tabla en la base de datos
    protected $primaryKey = 'id_plan_pago'; // Clave primaria de la tabla

    protected $allowedFields = [
        'valor_articulo',
        'valor_prima',
        'saldo_a_pagar',
        'cuotas',
        'monto_cuotas',
        'monto_total_pagar',
        'observaciones',
        'id_solicitud'
    ];

    public function buscarPorSolicitud($id_solicitud)
    {
        return $this->where('id_solicitud', $id_solicitud)->findAll();
    }
}

