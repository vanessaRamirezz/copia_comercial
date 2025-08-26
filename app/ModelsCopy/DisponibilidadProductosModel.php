<?php

namespace App\Models;

use CodeIgniter\Model;

class DisponibilidadProductosModel extends Model
{
    protected $table = 'productos'; // Nombre de tabla principal no relevante aquí
    protected $primaryKey = 'id_producto'; // Clave primaria no relevante para esta consulta

    public function getDisponibilidadProductosSucursal($sucursalId, $codigoProducto = null)
    {
        if (!$sucursalId) {
            return [];
        }

        $params = [];
        $sql = $this->buildDisponibilidadQuery($codigoProducto !== null);

        if ($codigoProducto !== null) {
            $params = [$codigoProducto, $sucursalId];
        } else {
            $params = [$sucursalId];
        }

        return $this->db->query($sql, $params)->getResult();
    }

    private function buildDisponibilidadQuery(bool $filtrarPorProducto): string
    {
        $filtroProducto = $filtrarPorProducto ? "p.codigo_producto = ? AND" : "";

        return "
        SELECT 
            p.id_producto,
            p.nombre,
            IFNULL(p.marca, '') AS marca,
            IFNULL(p.modelo, '') AS modelo,
            IFNULL(p.color, '') AS color,
            IFNULL(p.medidas, '') AS medidas,
            p.precio,
            p.costo_unitario,
            IFNULL(p.id_categoria, 0) AS id_categoria,
            p.disponible,
            IFNULL(p.codigo_producto, '') AS codigo_producto,
            p.estado,
            p.id_usuario_creacion,
            p.fecha_creacion,
            IFNULL(p.upc, '') AS upc,
            k.id_sucursal_movimiento,
            k.existencia AS disponibilidad
        FROM productos p
        JOIN (
            SELECT 
                m.id_producto,
                m.id_sucursal_movimiento,
                SUM(
                    CASE 
                        WHEN tm.tipo_mov IN (0, 1, 2) THEN m.cantidad
                        ELSE -m.cantidad
                    END
                ) AS existencia
            FROM movimientos m
            JOIN tipos_movimiento tm ON m.id_tipo_movimiento = tm.id_tipo_movimiento
            JOIN productos p ON m.id_producto = p.id_producto
            WHERE {$filtroProducto} m.id_sucursal_movimiento = ?
            GROUP BY m.id_producto, m.id_sucursal_movimiento
        ) AS k ON k.id_producto = p.id_producto
        " . ($filtrarPorProducto ? "LIMIT 1" : "");
    }



    public function getTotalSalidasPorSolicitud($idSucursal = null, $codigoProducto = null)
    {

        $builder = $this->db->table('productos p');
        $builder->select('
            SUM(CASE
                WHEN tm.tipo_mov IS NULL THEN m.cantidad
                ELSE 0
            END) AS totalSalida
        ');
        $builder->join('movimientos m', 'p.id_producto = m.id_producto');
        $builder->join('tipos_movimiento tm', 'm.id_tipo_movimiento = tm.id_tipo_movimiento');
        $builder->join('solicitud s', 'm.id_solicitud = s.id_solicitud');
        $builder->groupBy('m.id_solicitud,p.id_producto,p.nombre,p.codigo_producto,p.id_categoria,p.estado');

        $builder->where('m.id_documento IS NULL');
        if ($codigoProducto !== null) {
            $builder->where('p.codigo_producto', $codigoProducto);
        }

        if ($idSucursal !== null) {
            $builder->where('s.id_sucursal', $idSucursal);
        }

        $query = $builder->get();
        return $query->getResult();
    }
}
