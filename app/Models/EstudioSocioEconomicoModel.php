<?php

namespace App\Models;

use CodeIgniter\Model;

class EstudioSocioEconomicoModel extends Model
{
    // Nombre de la tabla en la base de datos
    protected $table = 'analisis_socioeconomico';
    protected $primaryKey = 'id_analisis_socioeconomico';

    // Campos permitidos para ser insertados o actualizados
    protected $allowedFields = [
        'id_solicitud',
        'ingreso_mensual',
        'egreso_mensual',
        'salario',
        'pago_casa',
        'otros_explicacion',
        'gastos_vida',
        'otros',
        'total_ingresos',
        'total_egresos',
        'diferencia_ingresos_egresos',
        'estado_financiero',
        'id_cliente'
    ];

    public function buscarPorSolicitud($id_solicitud)
    {
        return $this->where('id_solicitud', $id_solicitud)->findAll();
    }
}
