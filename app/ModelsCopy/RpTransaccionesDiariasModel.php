<?php

namespace App\Models;

use CodeIgniter\Model;

class RpTransaccionesDiariasModel extends Model
{
    protected $table = 'solicitud'; // tabla principal para que CI pueda usarla si se requiere
    protected $primaryKey = 'id_solicitud';
    protected $allowedFields = [
        'numero_solicitud',
        'id_cliente',
        'id_usuario_creacion',
        'id_estado_actual',
        'id_sucursal',
        'fecha_creacion',
        'monto_solicitud',
        'id_producto',
        'observacion',
        'montoApagar',
        'tipo_solicitud'
    ];

    public function getTransaccionesPorFecha($fechaInicio, $fechaFin)
    {
        return $this->db->table('solicitud as s')
            ->select("
            s.numero_solicitud AS Codigo,
            hc.descripcion AS Concepto,
            f.no_factura as Docum,
            s.tipo_solicitud AS Tipo,
            DATE_FORMAT(hc.fecha_registro, '%d/%m/%Y') AS Fecha,
            CASE 
                WHEN hc.descripcion LIKE 'ABONO%' THEN hc.abono 
                ELSE NULL 
            END AS abono,
            CASE 
                WHEN hc.descripcion LIKE 'PAGO%' THEN hc.abono 
                ELSE NULL 
            END AS pago,
            CASE 
                WHEN s.tipo_solicitud = 'CONTADO' THEN hc.abono 
                ELSE NULL 
            END AS Contado
        ")
            ->join('cobros as c', 's.id_solicitud = c.id_solicitud')
            ->join('historial_cobros as hc', 'c.id_cobro = hc.id_cobro')
            ->join('facturas as f', 'hc.id_factura = f.id_factura')
            ->where('DATE(hc.fecha_registro) >=', $fechaInicio)
            ->where('DATE(hc.fecha_registro) <=', $fechaFin)
            ->orderBy('hc.fecha_registro')
            ->get()
            ->getResultArray();
    }
}
