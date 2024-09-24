<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductosModel extends Model
{
    protected $table = 'productos';
    protected $primaryKey = 'id_producto';
    protected $allowedFields = ['nombre', 'marca', 'modelo', 'color', 'medidas', 'precio','costo_unitario','disponible', 'id_categoria', 'codigo_producto', 'estado','id_usuario_creacion','fecha_creacion'];

    public function getProductos()
    {
        $this->select('productos.*, categorias.nombre as nombre_categoria, usuarios.nombres as nombre_usuario');
        $this->join('categorias', 'categorias.id_categoria = productos.id_categoria');
        $this->join('usuarios', 'usuarios.id_usuario = productos.id_usuario_creacion');
        $this->orderBy('productos.id_producto', 'DESC');
        return $this->findAll();
    }    

    public function getProductosPorCodigoONombre($search){
        $this->select('productos.*, categorias.nombre as nombre_categoria, usuarios.nombres as nombre_usuario');
        $this->join('categorias', 'categorias.id_categoria = productos.id_categoria');
        $this->join('usuarios', 'usuarios.id_usuario = productos.id_usuario_creacion');
        $this->where('productos.estado', 1);
        $this->groupStart(); // Inicio del grupo de condiciones
        $this->like('productos.nombre', $search);
        $this->orLike('productos.codigo_producto', $search);
        $this->groupEnd(); // Fin del grupo de condiciones
        $this->orderBy('productos.id_producto', 'DESC');
        return $this->findAll();
    }

    public function getProductosXid($id_producto)
    {
        return $this->where('id_producto', $id_producto)->findAll();
    }
}
