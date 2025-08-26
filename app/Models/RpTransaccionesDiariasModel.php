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

    /* public function getTransaccionesPorFecha($fechaInicio, $fechaFin)
    {
        return $this->db->table('solicitud as s')
            ->select("
            s.numero_solicitud AS Codigo,
            CASE 
                WHEN s.tipo_solicitud = 'CONTADO' THEN cl.nombre_completo 
                ELSE hc.descripcion 
            END AS Concepto,
            f.no_factura as Docum,
            s.tipo_solicitud AS Tipo,
            DATE_FORMAT(hc.fecha_registro, '%d/%m/%Y') AS Fecha,
            CASE 
                WHEN hc.descripcion LIKE 'ABONO%' THEN hc.abono 
                ELSE NULL 
            END AS abono,
            CASE 
                WHEN hc.descripcion LIKE 'PAGO%' AND s.tipo_solicitud != 'CONTADO' THEN hc.abono 
                ELSE NULL 
            END AS pago,
            CASE 
                WHEN s.tipo_solicitud = 'CONTADO' THEN hc.abono 
                ELSE NULL 
            END AS Contado,
            CASE 
                WHEN c.estado = 'CANCELADO' THEN c.interesGenerado 
                ELSE 0 
            END AS Interes

        ")
            ->join('cobros as c', 's.id_solicitud = c.id_solicitud')
            ->join('historial_cobros as hc', 'c.id_cobro = hc.id_cobro')
            ->join('facturas as f', 'hc.id_factura = f.id_factura')
            ->join('clientes as cl', 's.id_cliente = cl.id_cliente')
            ->where('DATE(hc.fecha_registro) >=', $fechaInicio)
            ->where('DATE(hc.fecha_registro) <=', $fechaFin)
            ->orderBy('hc.fecha_registro')
            ->get()
            ->getResultArray();
    } */
    public function getTransaccionesPorFecha($fechaInicio, $fechaFin)
    {
        $session = session();
        $idSucursal = $session->get('sucursal'); // Obtener sucursal desde la sesión

        return $this->db->table('solicitud as s')
            ->select("
            s.numero_solicitud AS Codigo,
            c.esPrima as prima,
            CASE 
                WHEN s.tipo_solicitud = 'CONTADO' THEN cl.nombre_completo 
                ELSE hc.descripcion 
            END AS Concepto,
            f.no_factura as Docum,
            s.tipo_solicitud AS Tipo,
            DATE_FORMAT(hc.fecha_registro, '%d/%m/%Y') AS Fecha,
            CASE 
                WHEN hc.descripcion LIKE 'ABONO%' THEN hc.abono 
                ELSE NULL 
            END AS abono,
            CASE 
                WHEN hc.descripcion LIKE 'PAGO%' AND s.tipo_solicitud != 'CONTADO' AND c.esPrima = 0 THEN 
                    CASE 
                        WHEN c.estado = 'CANCELADO' THEN hc.abono - c.interesGenerado 
                        ELSE hc.abono 
                    END 
                WHEN hc.descripcion LIKE 'Crédito cancelado%' THEN hc.abono
                ELSE NULL  
            END AS pago,
            CASE 
                WHEN s.tipo_solicitud = 'CONTADO' THEN hc.abono 
                ELSE NULL 
            END AS Contado,
            CASE 
                WHEN c.estado = 'CANCELADO' THEN c.interesGenerado 
                ELSE 0 
            END AS Interes
        ")
            ->select("CASE 
            WHEN c.esPrima = 1 AND hc.descripcion NOT LIKE 'ABONO%' THEN hc.abono 
            ELSE NULL 
        END AS PrimaCa", false)
            ->join('cobros as c', 's.id_solicitud = c.id_solicitud')
            ->join('historial_cobros as hc', 'c.id_cobro = hc.id_cobro')
            ->join('facturas as f', 'hc.id_factura = f.id_factura')
            ->join('clientes as cl', 's.id_cliente = cl.id_cliente')
            //->where('s.id_sucursal', $idSucursal) // ← Aquí se filtra por sucursal
            ->where('hc.id_sucursal_proceso', $idSucursal)
            ->where('DATE(hc.fecha_registro) >=', $fechaInicio)
            ->where('DATE(hc.fecha_registro) <=', $fechaFin)
            ->orderBy('hc.fecha_registro')
            ->get()
            ->getResultArray();
    }
}
