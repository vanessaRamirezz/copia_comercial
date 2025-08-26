<?php

namespace App\Models;

use CodeIgniter\Model;

class RpVentasMensualesModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    public function getReporteVentasMensuales($fechaInicio, $fechaFin, $idSucursal)
    {
        // Asegurarse de que los valores estén escapados para evitar inyecciones SQL
        $fechaInicio = $this->db->escape($fechaInicio); // agrega comillas y escapa
        $fechaFin    = $this->db->escape($fechaFin);
        $idSucursal  = $this->db->escape($idSucursal);  // si es numérico no lleva comillas, pero escapa igual

        $sql = "
        SELECT 
            'CRÉDITO' AS tipo,
            CONCAT(u.nombres, ' ', u.apellidos) AS duiUsuario,
            s.fecha_creacion AS fecha,
            c.nombre_completo COLLATE utf8mb4_unicode_ci AS cliente,
            cs.num_contrato COLLATE utf8mb4_unicode_ci AS documento,
            s.monto_solicitud AS monto,
            1 AS orden
        FROM solicitud s
        INNER JOIN contrato_solicitud cs ON cs.id_solicitud = s.id_solicitud
        INNER JOIN clientes c ON c.id_cliente = s.id_cliente
        INNER JOIN usuarios u ON s.id_usuario_creacion = u.id_usuario
        WHERE s.tipo_solicitud = 'CREDITO'
          AND s.fecha_creacion BETWEEN $fechaInicio AND $fechaFin
          AND s.id_sucursal = $idSucursal

        UNION ALL

        SELECT 
            'CONTADO' AS tipo,
            CONCAT(u.nombres, ' ', u.apellidos) AS duiUsuario,
            s.fecha_creacion AS fecha,
            c.nombre_completo COLLATE utf8mb4_unicode_ci AS cliente,
            f.no_factura COLLATE utf8mb4_unicode_ci AS documento,
            hc.abono AS monto,
            2 AS orden
        FROM solicitud s
        INNER JOIN cobros cb ON cb.id_solicitud = s.id_solicitud
        INNER JOIN historial_cobros hc ON hc.id_cobro = cb.id_cobro
        INNER JOIN facturas f ON f.id_factura = hc.id_factura
        INNER JOIN clientes c ON c.id_cliente = s.id_cliente
        INNER JOIN usuarios u ON s.id_usuario_creacion = u.id_usuario
        WHERE s.tipo_solicitud = 'CONTADO'
          AND s.fecha_creacion BETWEEN $fechaInicio AND $fechaFin
          AND s.id_sucursal = $idSucursal

        ORDER BY orden, fecha
    ";

        log_message('debug', "Consulta ejecutada:\n" . $sql);

        $query = $this->db->query($sql);
        $resultados = $query->getResult();

        log_message('debug', 'Resultado obtenido: ' . json_encode($resultados));

        return $resultados;
    }

    public function getReporteVentasXvendedor($fechaInicio, $fechaFin, $idUsuarioCreacion)
    {
        // Escapar valores para seguridad
        $fechaInicio = $this->db->escape($fechaInicio);
        $fechaFin = $this->db->escape($fechaFin);
        $idUsuarioCreacion = $this->db->escape($idUsuarioCreacion);

        $sql = "
            SELECT 
                'CRÉDITO' AS tipo,
                s.fecha_creacion AS fecha,
                c.nombre_completo COLLATE utf8mb4_unicode_ci AS cliente,
                cs.num_contrato COLLATE utf8mb4_unicode_ci AS documento,
                s.monto_sin_prima AS monto,
                suc.sucursal COLLATE utf8mb4_unicode_ci AS sucursal,
                1 AS orden
            FROM solicitud s
            INNER JOIN contrato_solicitud cs ON cs.id_solicitud = s.id_solicitud
            INNER JOIN clientes c ON c.id_cliente = s.id_cliente
            INNER JOIN sucursal suc ON suc.id_sucursal = s.id_sucursal
            WHERE s.tipo_solicitud = 'CREDITO'
            AND s.fecha_creacion BETWEEN $fechaInicio AND $fechaFin
            AND s.id_usuario_creacion = $idUsuarioCreacion

            UNION ALL

            SELECT 
                'CONTADO' AS tipo,
                s.fecha_creacion AS fecha,
                c.nombre_completo COLLATE utf8mb4_unicode_ci AS cliente,
                f.no_factura COLLATE utf8mb4_unicode_ci AS documento,
                hc.abono AS monto,
                suc.sucursal COLLATE utf8mb4_unicode_ci AS sucursal,
                2 AS orden
            FROM solicitud s
            INNER JOIN cobros cb ON cb.id_solicitud = s.id_solicitud
            INNER JOIN historial_cobros hc ON hc.id_cobro = cb.id_cobro
            INNER JOIN facturas f ON f.id_factura = hc.id_factura
            INNER JOIN clientes c ON c.id_cliente = s.id_cliente
            INNER JOIN sucursal suc ON suc.id_sucursal = s.id_sucursal
            WHERE s.tipo_solicitud = 'CONTADO'
            AND s.fecha_creacion BETWEEN $fechaInicio AND $fechaFin
            AND s.id_usuario_creacion = $idUsuarioCreacion

            ORDER BY orden, fecha
            ";

        log_message('debug', "Consulta ejecutada:\n" . $sql);

        $query = $this->db->query($sql);
        $resultados = $query->getResult();

        log_message('debug', 'Resultado obtenido: ' . json_encode($resultados));

        return $resultados;
    }

    public function obtenerMovimientosFiltrados($fechaDesde, $fechaHasta)
    {
        $session = session();
        $idSucursal = $session->get('sucursal'); // Obtener sucursal desde la sesión
        $builder = $this->db->table('movimientos m');

        $builder->select('p.nombre AS nombre_producto', false);
        $builder->select('m.fecha AS fecha_mov', false);
        $builder->select('tp.descripcion AS des_mov', false);
        $builder->select('p.codigo_producto AS codProd', false);
        $builder->select('m.cantidad AS cantMov', false);
        $builder->select('p.precio AS precio', false);

        // CASE para sucursal del movimiento
        $builder->select("
        CASE 
            WHEN m.id_tipo_movimiento = 1 THEN NULL
            ELSE s1.sucursal
        END AS suc_mov
    ", false);

        // CASE para sucursal origen (si tipo = 1)
        $builder->select("
        CASE 
            WHEN m.id_tipo_movimiento = 1 THEN s2.sucursal
            ELSE NULL
        END AS suc_origen_doc
    ", false);

        // CASE para sucursal destino (si tipo = 1)
        $builder->select("
        CASE 
            WHEN m.id_tipo_movimiento = 1 THEN s3.sucursal
            ELSE NULL
        END AS suc_destino_doc
    ", false);

        // CASE para mostrar número de documento o número de solicitud
        $builder->select("
        CASE 
            WHEN m.id_documento IS NOT NULL THEN d.noDocumento
            WHEN m.id_solicitud IS NOT NULL THEN sol.numero_solicitud
            ELSE NULL
        END AS noDocumento
    ", false);

        // JOINs necesarios
        $builder->join('tipos_movimiento tp', 'm.id_tipo_movimiento = tp.id_tipo_movimiento');
        $builder->join('productos p', 'm.id_producto = p.id_producto');
        $builder->join('sucursal s1', 'm.id_sucursal_movimiento = s1.id_sucursal', 'left');
        $builder->join('documentos d', 'm.id_documento = d.id_documento', 'left');
        $builder->join('sucursal s2', 'd.id_sucursal_origen = s2.id_sucursal', 'left');
        $builder->join('sucursal s3', 'd.id_sucursal_destino = s3.id_sucursal', 'left');
        $builder->join('solicitud sol', 'm.id_solicitud = sol.id_solicitud', 'left');
        $builder->where("m.id_sucursal_movimiento =", $idSucursal);
        // Filtro por fecha (sin hora)
        if ($fechaDesde && $fechaHasta) {
            $builder->where("DATE(m.fecha) >=", $fechaDesde);
            $builder->where("DATE(m.fecha) <=", $fechaHasta);
        }

        return $builder->get()->getResultArray();
    }


   public function obtenerResumenPorSucursal($fechaInicio, $fechaFin)
{
    $db = \Config\Database::connect();

    $fechaInicioFull = $fechaInicio . ' 00:00:00';
    $fechaFinFull    = $fechaFin . ' 23:59:59';

    // --- Contado ---
    $contado = $db->table('solicitud s')
        ->select("s.id_sucursal, SUM(CASE WHEN s.tipo_solicitud = 'CONTADO' THEN s.monto_solicitud ELSE 0 END) AS Contado, COUNT(CASE WHEN s.tipo_solicitud = 'CONTADO' THEN 1 END) AS ItemsContado", false)
        ->where("DATE(s.fecha_creacion) BETWEEN '$fechaInicio' AND '$fechaFin'")
        ->groupBy('s.id_sucursal')
        ->get()
        ->getResultArray();

    // --- Primas ---
    $primas = $db->table('historial_cobros hc')
        ->select("hc.id_sucursal_proceso AS id_sucursal, SUM(CASE WHEN c.esPrima = 1 AND c.estado = 'CANCELADO' THEN hc.abono ELSE 0 END) AS Primas, COUNT(CASE WHEN c.esPrima = 1 AND c.estado = 'CANCELADO' THEN 1 END) AS ItemsPrimas", false)
        ->join('cobros c', 'c.id_cobro = hc.id_cobro')
        ->where("hc.fecha_registro BETWEEN '$fechaInicioFull' AND '$fechaFinFull'")
        ->groupBy('hc.id_sucursal_proceso')
        ->get()
        ->getResultArray();

    // --- Cuotas ---
$cuotas = $db->table('historial_cobros hc')
    ->select("
        hc.id_sucursal_proceso AS id_sucursal,
        SUM(hc.abono) AS Cuotas,
        COUNT(DISTINCT hc.id_factura) AS ItemsCuotas
    ", false)
    ->join('cobros c', 'c.id_cobro = hc.id_cobro')
    ->where("hc.fecha_registro BETWEEN '$fechaInicioFull' AND '$fechaFinFull'")
    ->where("c.esPrima", 0) // solo las que no son primas
    ->where("hc.descripcion NOT LIKE '%contado%'") // que no tengan 'contado' en la descripción
    ->groupBy('hc.id_sucursal_proceso')
    ->get()
    ->getResultArray();



    // --- Combinar resultados en PHP ---
    $resumen = [];

    // Inicializar con Contado
    foreach ($contado as $c) {
        $idSuc = $c['id_sucursal'];
        $resumen[$idSuc] = [
            'id_sucursal'  => $idSuc,
            'Contado'      => $c['Contado'],
            'ItemsContado' => $c['ItemsContado'],
            'Primas'       => 0,
            'ItemsPrimas'  => 0,
            'Cuotas'       => 0,
            'ItemsCuotas'  => 0,
        ];
    }

    // Agregar Primas
    foreach ($primas as $p) {
        $idSuc = $p['id_sucursal'];
        if (!isset($resumen[$idSuc])) {
            $resumen[$idSuc] = [
                'id_sucursal'  => $idSuc,
                'Contado'      => 0,
                'ItemsContado' => 0,
                'Primas'       => 0,
                'ItemsPrimas'  => 0,
                'Cuotas'       => 0,
                'ItemsCuotas'  => 0,
            ];
        }
        $resumen[$idSuc]['Primas']      = $p['Primas'];
        $resumen[$idSuc]['ItemsPrimas'] = $p['ItemsPrimas'];
    }

    // Agregar Cuotas
    foreach ($cuotas as $q) {
        $idSuc = $q['id_sucursal'];
        if (!isset($resumen[$idSuc])) {
            $resumen[$idSuc] = [
                'id_sucursal'  => $idSuc,
                'Contado'      => 0,
                'ItemsContado' => 0,
                'Primas'       => 0,
                'ItemsPrimas'  => 0,
                'Cuotas'       => 0,
                'ItemsCuotas'  => 0,
            ];
        }
        $resumen[$idSuc]['Cuotas']      = $q['Cuotas'];
        $resumen[$idSuc]['ItemsCuotas'] = $q['ItemsCuotas'];
    }

    // --- Traer nombres de sucursal ---
    $sucursales = $db->table('sucursal')->select('id_sucursal, sucursal')->get()->getResultArray();
    $mapSuc = array_column($sucursales, 'sucursal', 'id_sucursal');

    // Reemplazar id por nombre
    foreach ($resumen as &$r) {
        $r['sucursal'] = $mapSuc[$r['id_sucursal']] ?? 'Desconocida';
        // Calcular totales
        $r['Total'] = $r['Contado'] + $r['Primas'] + $r['Cuotas'];
        $r['ItemsTotal'] = $r['ItemsContado'] + $r['ItemsPrimas'] + $r['ItemsCuotas'];
    }

    return array_values($resumen);
}

}
