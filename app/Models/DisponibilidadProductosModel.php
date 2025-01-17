<?php

namespace App\Models;

use CodeIgniter\Model;

class DisponibilidadProductosModel extends Model
{
    protected $table = 'productos'; // Nombre de tabla principal no relevante aquí
    protected $primaryKey = 'id_producto'; // Clave primaria no relevante para esta consulta

    public function getDisponibilidadProductosSucursal($codigoProducto, $idSucursalDestino)
    {
        $builder = $this->db->table('productos p');
        $builder->select('
            d.id_sucursal_destino AS id_sucursal_destino,
            p.*,
            SUM(CASE
                WHEN tm.tipo_mov = 0 OR tm.tipo_mov = 1 OR tm.tipo_mov = 2
                THEN m.cantidad
                ELSE - m.cantidad
            END) AS disponibilidad
        ');
        $builder->join('movimientos m', 'p.id_producto = m.id_producto');
        $builder->join('documentos d', 'm.id_documento = d.id_documento');
        $builder->join('tipos_movimiento tm', 'm.id_tipo_movimiento = tm.id_tipo_movimiento');
        $builder->groupBy('p.id_producto, p.nombre, p.codigo_producto, p.id_categoria, p.estado, d.id_sucursal_destino');

        // Agregar filtros
        if ($codigoProducto !== null) {
            $builder->where('p.codigo_producto', $codigoProducto);
        }
        if ($idSucursalDestino !== null) {
            $builder->where('d.id_sucursal_destino', $idSucursalDestino);
        }
        // Imprimir la consulta SQL generada
        log_message("info", "query " . $this->db->getLastQuery());

        $query = $builder->get();
        return $query->getResult();
    }

    public function getTotalSalidasPorSolicitud($codigoProducto = null, $idSucursal = null)
    {

        $builder = $this->db->table('productos p');
        $builder->select('
            SUM(CASE
                WHEN tm.tipo_mov = 11 THEN m.cantidad
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
