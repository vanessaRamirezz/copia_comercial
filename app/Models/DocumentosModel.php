<?php

namespace App\Models;

use CodeIgniter\Model;

class DocumentosModel extends Model
{
    protected $table = 'documentos';
    protected $primaryKey = 'id_documento';
    protected $allowedFields = ['id_usuario_creacion', 'id_sucursal_destino', 'id_sucursal_origen', 'id_proveedor', 'monto_total', 'estado', 'noDocumento', 'correlativo', 'observaciones', 'id_tipo_movimiento'];

    public function insertDocumento($data)
    {
        $this->insert($data);
        return $this->insertID(); // Devuelve el ID del documento insertado
    }

    public function insertProductoDocumento($data)
    {
        return $this->db->table('productos_documentos')->insert($data);
    }

    public function obtenerDocumentos()
    {
        $this->distinct();
        $this->select('d.*, u.nombres as usuario, s.sucursal, p.nombre as proveedor, tm.descripcion AS nombre_movimiento');
        $this->from('documentos as d');
        $this->join('usuarios as u', 'd.id_usuario_creacion = u.id_usuario');
        $this->join('sucursal as s', 's.id_sucursal = d.id_sucursal_destino');
        $this->join('proveedores as p', 'p.id_proveedor = d.id_proveedor','left');
        $this->join('tipos_movimiento as tm', 'tm.id_tipo_movimiento = d.id_tipo_movimiento');
        $this->where('d.id_tipo_movimiento !=', 1);
        return $this->findAll();
    }

    public function obtenerDocumentosPorId($idTipoMovimiento)
    {
        $this->distinct();
        $this->select('d.*, u.nombres as usuario, s.sucursal, p.nombre as proveedor, tm.descripcion AS nombre_movimiento');
        $this->from('documentos as d');
        $this->join('usuarios as u', 'd.id_usuario_creacion = u.id_usuario');
        $this->join('sucursal as s', 's.id_sucursal = d.id_sucursal_destino');
        $this->join('proveedores as p', 'p.id_proveedor = d.id_proveedor', 'left');
        $this->join('tipos_movimiento as tm', 'tm.id_tipo_movimiento = d.id_tipo_movimiento');
        $this->where('d.id_tipo_movimiento', $idTipoMovimiento);
        return $this->findAll();
    }


    public function obtenerProductosXDocumentos($idDocumento)
    {
        log_message('debug', "Obteniendo productos para el documento ID: " . $idDocumento); // Log adicional
        $this->distinct();
        $this->select('p.codigo_producto, pd.cantidad, p.nombre, p.modelo, p.precio');
        $this->from('documentos as d');
        $this->join('productos_documentos pd', 'd.id_documento = pd.id_documento');
        $this->join('productos p', 'p.id_producto = pd.id_producto');
        $this->where('d.id_documento', $idDocumento);
        $result = $this->findAll();
        log_message('debug', "Resultado de productos obtenidos: " . print_r($result, true)); // Log adicional
        return $result;
    }


    public function obtenerDocumentoPorId($idDocumento)
    {
        $this->distinct();
        $this->select('d.*, u.nombres as usuario, s.sucursal, p.nombre as proveedor');
        $this->from('documentos as d');
        $this->join('usuarios as u', 'd.id_usuario_creacion = u.id_usuario');
        $this->join('sucursal as s', 's.id_sucursal = d.id_sucursal_destino');
        $this->join('proveedores as p', 'p.id_proveedor = d.id_proveedor','left');
        return $this->where('d.id_documento', $idDocumento)->first();
    }

    public function obtenerDocumentoPorIdNotaRemiEntrada($idDocumento)
    {
        $this->distinct();
        $this->select('d.*, u.nombres as usuario, s_destino.sucursal as sucursal_destino, s_origen.sucursal as sucursal_origen, p.nombre as proveedor');
        $this->from('documentos as d');
        $this->join('usuarios as u', 'd.id_usuario_creacion = u.id_usuario');
        $this->join('sucursal as s_destino', 's_destino.id_sucursal = d.id_sucursal_destino');
        $this->join('sucursal as s_origen', 's_origen.id_sucursal = d.id_sucursal_origen');
        $this->join('proveedores as p', 'p.id_proveedor = d.id_proveedor', 'left');
        return $this->where('d.id_documento', $idDocumento)->first();
    }
    public function obtenerDocumentoPorIdNotaRemiSalida($idDocumento)
    {
        $this->distinct();
        $this->select('d.*, u.nombres as usuario, s_destino.sucursal as sucursal_destino, s_origen.sucursal as sucursal_origen, p.nombre as proveedor');
        $this->from('documentos as d');
        $this->join('usuarios as u', 'd.id_usuario_creacion = u.id_usuario');
        $this->join('sucursal as s_destino', 's_destino.id_sucursal = d.id_sucursal_origen');
        $this->join('sucursal as s_origen', 's_origen.id_sucursal = d.id_sucursal_destino');
        $this->join('proveedores as p', 'p.id_proveedor = d.id_proveedor','left');
        return $this->where('d.id_documento', $idDocumento)->first();
    }
}
