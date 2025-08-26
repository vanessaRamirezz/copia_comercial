<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductoSolicitudModel extends Model
{
    protected $table = 'productos_solicitud'; // Nombre de la tabla
    protected $primaryKey = 'id_producto_solicitud'; // Clave primaria

    protected $allowedFields = ['id_producto', 'cantidad_producto', 'id_solicitud', 'precio_producto'];

    /* public function buscarPorSolicitud($id_solicitud)
    {
        // Construir la consulta para buscar por id_solicitud
        return $this->select('*')
            ->where('id_solicitud', $id_solicitud)
            ->join('productos', 'productos.id_producto = productos_solicitud.id_producto')
            ->findAll();
    } */

    public function buscarPorSolicitud($id_solicitud)
    {
        return $this->select('productos_solicitud.*, productos.*, movimientos.p_contado')
            ->join('productos', 'productos.id_producto = productos_solicitud.id_producto')
            ->join('movimientos', 'movimientos.id_producto = productos.id_producto AND movimientos.id_solicitud = productos_solicitud.id_solicitud')
            ->where('productos_solicitud.id_solicitud', $id_solicitud)
            ->findAll();
    }


    public function buscarPorSolicitudDescripcion($id_solicitud)
    {
        return $this->select([
            'productos_solicitud.id_producto',
            'productos.nombre',
            'productos.precio',
            'productos_solicitud.cantidad_producto'
        ])
            ->join('productos', 'productos.id_producto = productos_solicitud.id_producto')
            ->where('productos_solicitud.id_solicitud', $id_solicitud)
            ->findAll();
    }

    public function buscarProdSolicitud($idSolicitudSelect)
    {
        $builder = $this->db->table('productos_solicitud');
        $builder->select("GROUP_CONCAT(productos.nombre SEPARATOR ', ') as descripcion");
        $builder->join('productos', 'productos.id_producto = productos_solicitud.id_producto');
        $builder->join('movimientos', 'movimientos.id_producto = productos.id_producto AND movimientos.id_solicitud = productos_solicitud.id_solicitud');
        $builder->where('productos_solicitud.id_solicitud', $idSolicitudSelect);

        $query = $builder->get();
        return $query->getRow();
    }
}
