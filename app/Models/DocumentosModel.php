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

    /* public function obtenerDocumentos()
    {
        $this->distinct();
        $this->select('d.*, u.nombres as usuario, s.sucursal, p.nombre as proveedor, tm.descripcion AS nombre_movimiento');
        $this->from('documentos as d');
        $this->join('usuarios as u', 'd.id_usuario_creacion = u.id_usuario');
        $this->join('sucursal as s', 's.id_sucursal = d.id_sucursal_destino');
        $this->join('proveedores as p', 'p.id_proveedor = d.id_proveedor', 'left');
        $this->join('tipos_movimiento as tm', 'tm.id_tipo_movimiento = d.id_tipo_movimiento');
        $this->where('d.id_tipo_movimiento !=', 1);
        return $this->findAll();
    } */

    public function obtenerDocumentos()
    {
        $session = session();
        $idSucursal = $session->get('sucursal');

        $this->distinct();
        $this->select("
        d.*,
        u.nombres AS usuario,
        s_destino.sucursal AS sucursal_destino,
        s_origen.sucursal AS sucursal_origen,
        p.nombre AS proveedor,
        tm.descripcion AS nombre_movimiento
    ");
        $this->from('documentos as d');
        $this->join('usuarios as u', 'd.id_usuario_creacion = u.id_usuario');
        $this->join('sucursal as s_destino', 's_destino.id_sucursal = d.id_sucursal_destino', 'left');
        $this->join('sucursal as s_origen', 's_origen.id_sucursal = d.id_sucursal_origen', 'left');
        $this->join('proveedores as p', 'p.id_proveedor = d.id_proveedor', 'left');
        $this->join('tipos_movimiento as tm', 'tm.id_tipo_movimiento = d.id_tipo_movimiento');
        $this->where('d.id_tipo_movimiento !=', 1);
        $this->groupStart()
            ->where('d.id_sucursal_destino', $idSucursal)
            ->orWhere('d.id_sucursal_origen', $idSucursal)
            ->groupEnd();

        // Condición: al menos uno de los dos id_sucursal no es null
        $this->groupStart()
            ->where('d.id_sucursal_destino IS NOT NULL')
            ->orWhere('d.id_sucursal_origen IS NOT NULL')
            ->groupEnd();

        // Evitar duplicados
        $this->groupBy('d.correlativo');

        return $this->findAll();
    }



    /* public function obtenerDocumentosPorId($idTipoMovimiento)
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
    } */

    /* public function obtenerDocumentosPorId($idTipoMovimiento)
    {
        $this->distinct();
        $this->select('d.*, u.nombres as usuario, s.sucursal, p.nombre as proveedor, tm.descripcion AS nombre_movimiento');
        $this->from('documentos as d');
        $this->join('usuarios as u', 'd.id_usuario_creacion = u.id_usuario');
        $this->join('sucursal as s', 's.id_sucursal = COALESCE(d.id_sucursal_destino, d.id_sucursal_origen)', 'inner', false);
        $this->join('proveedores as p', 'p.id_proveedor = d.id_proveedor', 'left');
        $this->join('tipos_movimiento as tm', 'tm.id_tipo_movimiento = d.id_tipo_movimiento');
        $this->where('d.id_tipo_movimiento', $idTipoMovimiento);

        return $this->findAll();
    } */

    public function obtenerDocumentosPorId($idTipoMovimiento)
{
    $session = session();
    $idSucursal = $session->get('sucursal'); // ajusta si tu variable es distinta

    $this->distinct();
    $this->select("
        d.*,
        u.nombres AS usuario,
        s_destino.sucursal AS sucursal_destino,
        s_origen.sucursal AS sucursal_origen,
        p.nombre AS proveedor,
        tm.descripcion AS nombre_movimiento
    ");
    $this->from('documentos as d');
    $this->join('usuarios as u', 'd.id_usuario_creacion = u.id_usuario');
    $this->join('sucursal as s_destino', 's_destino.id_sucursal = d.id_sucursal_destino', 'left');
    $this->join('sucursal as s_origen', 's_origen.id_sucursal = d.id_sucursal_origen', 'left');
    $this->join('proveedores as p', 'p.id_proveedor = d.id_proveedor', 'left');
    $this->join('tipos_movimiento as tm', 'tm.id_tipo_movimiento = d.id_tipo_movimiento');

    // Solo traer el tipo de movimiento solicitado
    $this->where('d.id_tipo_movimiento', $idTipoMovimiento);

    // Filtro sucursal: traer solo si al menos uno de los dos campos coincide
    $this->groupStart()
        ->where('d.id_sucursal_destino', $idSucursal)
        ->orWhere('d.id_sucursal_origen', $idSucursal)
        ->groupEnd();

    // Evitar traer documentos donde ambos campos de sucursal sean NULL
    $this->groupStart()
        ->where('d.id_sucursal_destino IS NOT NULL')
        ->orWhere('d.id_sucursal_origen IS NOT NULL')
        ->groupEnd();

    // Ordenar por id descendente (últimos ingresados primero)
    $this->orderBy('d.id_documento', 'DESC');

    return $this->findAll();
}


    public function obtenerProcesadosSucursal()
    {
        $session = session();
        $idSucursal = $session->get('sucursal');

        $this->distinct();
        $this->select("
        d.*,
        u.nombres AS usuario,
        s_destino.sucursal AS sucursal_destino,
        s_origen.sucursal AS sucursal_origen,
        p.nombre AS proveedor,
        tm.descripcion AS nombre_movimiento
    ");
        $this->from('documentos as d');
        $this->join('usuarios as u', 'd.id_usuario_creacion = u.id_usuario');
        $this->join('sucursal as s_destino', 's_destino.id_sucursal = d.id_sucursal_destino', 'left');
        $this->join('sucursal as s_origen', 's_origen.id_sucursal = d.id_sucursal_origen', 'left');
        $this->join('proveedores as p', 'p.id_proveedor = d.id_proveedor', 'left');
        $this->join('tipos_movimiento as tm', 'tm.id_tipo_movimiento = d.id_tipo_movimiento');

        // Solo documentos Procesados de tipo 6
        $this->where('d.id_tipo_movimiento', 6);
        $this->where('d.estado', 'Procesado');

        // Filtrar por sucursal: origen o destino
        $this->groupStart()
            ->where('d.id_sucursal_destino', $idSucursal)
            ->orWhere('d.id_sucursal_origen', $idSucursal)
            ->groupEnd();

        // Evitar duplicados por correlativo
        $this->groupBy('d.correlativo');

        // Ordenar por correlativo descendente
        //$this->orderBy('d.correlativo', 'DESC');
        // Ordenar por id descendente (últimos ingresados primero)
        $this->orderBy('d.id_documento', 'DESC');


        return $this->findAll();
    }






    public function obtenerProductosXDocumentos($idDocumento)
    {
        log_message('debug', "Obteniendo productos para el documento ID: " . $idDocumento); // Log adicional
        $this->distinct();
        $this->select('p.id_producto,p.codigo_producto, pd.cantidad, p.nombre, p.modelo, p.precio');
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
        $this->select('d.*, u.nombres as usuario, s.sucursal, p.nombre as proveedor, tm.tipo_mov');
        $this->from('documentos as d');
        $this->join('usuarios as u', 'd.id_usuario_creacion = u.id_usuario');
        /* $this->join('sucursal as s', 's.id_sucursal = d.id_sucursal_destino'); */
        $this->join('sucursal as s', 's.id_sucursal = COALESCE(d.id_sucursal_destino, d.id_sucursal_origen)', 'inner', false);
        $this->join('proveedores as p', 'p.id_proveedor = d.id_proveedor', 'left');
        $this->join('tipos_movimiento as tm', 'd.id_tipo_movimiento = tm.id_tipo_movimiento');
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
        $this->join('proveedores as p', 'p.id_proveedor = d.id_proveedor', 'left');
        return $this->where('d.id_documento', $idDocumento)->first();
    }

    public function countDocumentoProcesado($idSucursalDestino)
    {
        return $this->where('estado', 'Procesado')
            ->where('id_sucursal_destino', $idSucursalDestino)
            ->countAllResults();
    }

    public function obtenerCorrelativoAutomatico(int $idSucursal, int $idTipoMovimiento)
    {
        // Si es ingreso manual por compras (id_tipo_movimiento == 1), no generar correlativo automático
        if ($idTipoMovimiento === 1) {
            return null; // No aplica correlativo automático
        }

        // Determinar el campo de sucursal según el tipo de movimiento
        $campoSucursal = 'id_sucursal'; // Por defecto el campo para filtrar es 'id_sucursal'

        if ($idTipoMovimiento === 2 || $idTipoMovimiento === 6 || $idTipoMovimiento === 5 || $idTipoMovimiento === 7 || $idTipoMovimiento === 8 || $idTipoMovimiento === 9 || $idTipoMovimiento === 10) {
            $campoSucursal = 'id_sucursal_origen';
        } else if ($idTipoMovimiento === 4 || $idTipoMovimiento === 3) {
            $campoSucursal = 'id_sucursal_destino';
        }

        // Contar documentos con filtro dinámico de sucursal y tipo de movimiento
        $count = $this->where($campoSucursal, $idSucursal)
            ->where('id_tipo_movimiento', $idTipoMovimiento)
            ->countAllResults();

        // Correlativo = cantidad + 1
        $correlativo = $count + 1;

        // Formatear con ceros a la izquierda para que tenga 6 dígitos
        return str_pad($correlativo, 6, '0', STR_PAD_LEFT);
    }
}
