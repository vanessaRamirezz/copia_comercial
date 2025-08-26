<?php

namespace App\Models;

use CodeIgniter\Model;

class MovimientosModel extends Model
{
    protected $table = 'movimientos';
    protected $primaryKey = 'id_movimiento';
    protected $allowedFields = ['id_producto', 'id_tipo_movimiento', 'cantidad', 'fecha', 'descripcion', 'id_solicitud', 'id_documento', 'id_sucursal_movimiento'];

    public function getMovimientos()
    {
        return $this->findAll();
    }

    public function insertMovimiento($data)
    {
        return $this->insert($data);
    }

    public function getDescripcionById($id)
    {
        $result = $this->select('descripcion')->where('id_movimiento', $id)->first();
        return $result ? $result['descripcion'] : null;
    }

    public function getMovimientosBySolicitud($idSolicitud)
    {
        return $this->where('id_solicitud', $idSolicitud)->findAll();
    }


    public function getKardex($codigoProducto, $idSucursal)
    {
        $db = \Config\Database::connect();

        $sql = "
        SELECT 
            k.fecha,
            k.detalle,
            k.entrada,
            k.salida,
            k.existencia,
            k.costo_unitario,
            ROUND(k.cantidad_total * k.costo_unitario, 2) AS total,
            k.noDocumento
        FROM (
            SELECT 
                m.fecha,
                tm.descripcion AS detalle,
                CASE WHEN tm.tipo_mov IN (0, 1, 2) THEN m.cantidad ELSE 0 END AS entrada,
                CASE WHEN tm.tipo_mov IN (3, 6) THEN m.cantidad ELSE 0 END AS salida,
                @existencia := @existencia + 
                    (CASE WHEN tm.tipo_mov IN (0, 1, 2) THEN m.cantidad ELSE -m.cantidad END) AS existencia,
                p.costo_unitario,
                m.cantidad AS cantidad_total,
                d.noDocumento
            FROM movimientos m
            JOIN tipos_movimiento tm ON m.id_tipo_movimiento = tm.id_tipo_movimiento
            JOIN productos p ON m.id_producto = p.id_producto
            LEFT JOIN documentos d ON m.id_documento = d.id_documento
            CROSS JOIN (SELECT @existencia := 0) AS init
            WHERE p.codigo_producto = ?
              AND m.id_sucursal_movimiento = ?
            ORDER BY m.fecha, m.id_movimiento
        ) AS k
    ";

        $query = $db->query($sql, [$codigoProducto, $idSucursal]);
        return $query->getResultArray(); // Devuelve array asociativo por fila
    }
}
