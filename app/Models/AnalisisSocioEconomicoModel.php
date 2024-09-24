<?php

namespace App\Models;

use CodeIgniter\Model;

class AnalisisSocioEconomicoModel extends Model
{
    protected $table = 'analisis_socioeconomico';
    protected $primaryKey = 'id_analisis_socioeconomico';
    
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
}
