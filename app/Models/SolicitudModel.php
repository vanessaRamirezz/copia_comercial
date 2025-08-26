<?php

namespace App\Models;

use CodeIgniter\Model;

class SolicitudModel extends Model
{
    protected $table = 'solicitud';
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
        'tipo_solicitud',
        'monto_sin_prima',
        'detalle_series'
    ];

    // Configuración para devolver el ID creado después de la inserción
    protected $useAutoIncrement = true;

    public function solicitudPorSucursalEstadoCreadas($idSucursal)
    {
        return $this->select('DISTINCT(s.id_solicitud), s.id_solicitud, s.numero_solicitud, s.fecha_creacion, c.dui, c.nombre_completo, CONCAT(u.nombres, " ", u.apellidos) as user_creador, es.estado, s.id_estado_actual, s.observacion, num_contrato, s.montoApagar,s.tipo_solicitud')
            ->from('solicitud as s')
            ->join('clientes as c', 's.id_cliente = c.id_cliente')
            ->join('usuarios as u', 's.id_usuario_creacion = u.id_usuario')
            ->join('estados_solicitud as es', 's.id_estado_actual = es.id_estado')
            ->join('contrato_solicitud as cs', 'cs.numero_solicitud = s.numero_solicitud', 'left')
            ->where('s.id_sucursal', $idSucursal)
            ->where('s.tipo_solicitud', 'CREDITO') // Condición por tipo de solicitud
            ->where('s.id_estado_actual', 1)
            ->findAll();
    }

    /* public function solicitudPorSucursalEstadoVarias($idSucursal)
    {
        $limit = 10;

        return $this->select('DISTINCT(s.id_solicitud), s.id_solicitud, s.numero_solicitud, s.fecha_creacion, c.dui, c.nombre_completo, CONCAT(u.nombres, " ", u.apellidos) as user_creador, es.estado, s.id_estado_actual, s.observacion, num_contrato, s.montoApagar, s.tipo_solicitud')
            ->from('solicitud as s')
            ->join('clientes as c', 's.id_cliente = c.id_cliente')
            ->join('usuarios as u', 's.id_usuario_creacion = u.id_usuario')
            ->join('estados_solicitud as es', 's.id_estado_actual = es.id_estado')
            ->join('contrato_solicitud as cs', 'cs.numero_solicitud = s.numero_solicitud', 'left')
            ->where('s.id_sucursal', $idSucursal)
            ->where('s.tipo_solicitud', 'CREDITO')
            ->where('s.id_estado_actual !=', 1)
            ->orderBy('s.id_solicitud', 'DESC')
            ->limit($limit)
            ->get()
            ->getResult();
    } */
    public function solicitudPorSucursalEstadoVarias($idSucursal)
    {
        $limit = 10;

        // Subconsulta para obtener un solo contrato por solicitud (el más alto)
        $builder = $this->db->table('contrato_solicitud');
        $builder->select('numero_solicitud, MAX(num_contrato) as num_contrato');
        $builder->groupBy('numero_solicitud');

        $subquery = $builder->getCompiledSelect();

        // Consulta principal
        return $this->db->table('solicitud as s')
            ->select('s.id_solicitud, s.numero_solicitud, s.fecha_creacion, c.dui, c.nombre_completo, CONCAT(u.nombres, " ", u.apellidos) as user_creador, es.estado, s.id_estado_actual, s.observacion, cs.num_contrato, s.montoApagar, s.tipo_solicitud')
            ->join('clientes as c', 's.id_cliente = c.id_cliente')
            ->join('usuarios as u', 's.id_usuario_creacion = u.id_usuario')
            ->join('estados_solicitud as es', 's.id_estado_actual = es.id_estado')
            ->join("($subquery) cs", 'cs.numero_solicitud = s.numero_solicitud', 'left')
            ->where('s.id_sucursal', $idSucursal)
            ->where('s.tipo_solicitud', 'CREDITO')
            ->where('s.id_estado_actual !=', 1)
            ->orderBy('s.id_solicitud', 'DESC')
            ->limit($limit)
            ->get()
            ->getResult();
    }



    public function solCreadaContado($idSucursal)
    {
        $limit = 50;

        return $this->select('DISTINCT(s.id_solicitud), s.numero_solicitud, s.fecha_creacion, c.dui, c.nombre_completo, CONCAT(u.nombres, " ", u.apellidos) as user_creador, es.estado, s.id_estado_actual, s.observacion, s.monto_solicitud, s.tipo_solicitud, f.ruta_factura, s.detalle_series')
            ->from('solicitud as s')
            ->join('clientes as c', 's.id_cliente = c.id_cliente')
            ->join('usuarios as u', 's.id_usuario_creacion = u.id_usuario')
            ->join('estados_solicitud as es', 's.id_estado_actual = es.id_estado')
            ->join('cobros as co', 's.id_solicitud = co.id_solicitud', 'left')
            ->join('historial_cobros as hc', 'co.id_cobro = hc.id_cobro', 'left')
            ->join('facturas as f', 'hc.id_factura = f.id_factura', 'left')
            ->where('s.id_sucursal', $idSucursal)
            ->where('s.tipo_solicitud', 'CONTADO')
            ->where('s.id_estado_actual !=', 1)
            ->orderBy('s.id_solicitud', 'DESC')
            ->limit($limit)
            ->get()
            ->getResult();
    }


    public function countSolicitudes($idSucursal)
    {
        return $this->select('COUNT(DISTINCT(s.id_solicitud)) as total')
            ->from('solicitud as s')
            ->join('clientes as c', 's.id_cliente = c.id_cliente')
            ->join('usuarios as u', 's.id_usuario_creacion = u.id_usuario')
            ->join('estados_solicitud as es', 's.id_estado_actual = es.id_estado')
            ->join('contrato_solicitud as cs', 'cs.numero_solicitud = s.numero_solicitud', 'left')
            ->where('s.id_sucursal', $idSucursal)
            ->where('s.tipo_solicitud', 'CREDITO') // Condición por tipo de solicitud
            ->where('s.id_estado_actual !=', 1)  // Puedes ajustar esta condición si es necesario
            ->first();  // Devuelve el total de registros
    }



    public function solicitudAprobadasPorCliente($dui)
    {
        /* return $this->select('DISTINCT(s.id_solicitud), s.id_solicitud, s.numero_solicitud, s.fecha_creacion, c.dui, c.nombre_completo, CONCAT(u.nombres, " ", u.apellidos) as user_creador, es.estado, s.id_estado_actual, s.observacion, num_contrato, s.montoApagar,s.tipo_solicitud')
            ->from('solicitud as s')
            ->join('clientes as c', 's.id_cliente = c.id_cliente')
            ->join('usuarios as u', 's.id_usuario_creacion = u.id_usuario')
            ->join('estados_solicitud as es', 's.id_estado_actual = es.id_estado')
            ->join('contrato_solicitud as cs', 'cs.numero_solicitud = s.numero_solicitud', 'left')
            ->where('c.dui', $dui)
            ->whereIn('s.id_estado_actual', [2, 5])
            ->findAll(); */
        return $this->select('
            s.id_solicitud,
            s.numero_solicitud,
            s.fecha_creacion,
            c.dui,
            c.nombre_completo,
            CONCAT(u.nombres, " ", u.apellidos) as user_creador,
            es.estado,
            s.id_estado_actual,
            s.observacion,
            cs.num_contrato,
            s.montoApagar,
            s.tipo_solicitud,
            GROUP_CONCAT(DISTINCT p.codigo_producto SEPARATOR ", ") as codigos_productos,
            su.sucursal
        ')
            ->from('solicitud as s')
            ->join('clientes as c', 's.id_cliente = c.id_cliente')
            ->join('usuarios as u', 's.id_usuario_creacion = u.id_usuario')
            ->join('estados_solicitud as es', 's.id_estado_actual = es.id_estado')
            ->join('contrato_solicitud as cs', 'cs.numero_solicitud = s.numero_solicitud', 'left')
            ->join('productos_solicitud as ps', 'ps.id_solicitud = s.id_solicitud', 'left')
            ->join('productos as p', 'p.id_producto = ps.id_producto', 'left')
            ->join('sucursal as su', 's.id_sucursal = su.id_sucursal')
            ->where('c.dui', $dui)
            ->whereIn('s.id_estado_actual', [2, 5])
            ->groupBy('s.id_solicitud') // Agrupás para que GROUP_CONCAT funcione
            ->findAll();
    }

    /* public function getSolicitud($numeroSolicitud)
    {
        return $this->select('*') // Selecciona todos los campos
            ->where('numero_solicitud', $numeroSolicitud) // Condición por número de solicitud
            ->first(); // Devuelve un solo registro
    } */
    public function getSolicitud($numeroSolicitud)
    {
        $session = session(); // Obtener la sesión
        return $this->select('*') // Seleccionar todos los campos
            ->where('numero_solicitud', $numeroSolicitud) // Filtrar por número de solicitud
            ->where('id_sucursal', $session->get('sucursal')) // Obtener sucursal desde la sesión
            ->first(); // Devolver un solo registro
    }

    public function getSolicitudXid($id)
    {
        $session = session(); // Obtener la sesión
        return $this->select('*') // Seleccionar todos los campos
            ->where('id_solicitud', $id) // Filtrar por número de solicitud
            //->where('id_sucursal', $session->get('sucursal')) // Obtener sucursal desde la sesión
            ->first(); // Devolver un solo registro
    }


    public function getUltimaSolicitudCliente($id_cliente)
    {
        return $this->select('*') // Selecciona todos los campos
            ->where('id_cliente', $id_cliente) // Filtra por el cliente
            ->where('tipo_solicitud', 'CREDITO') // Condición por tipo de solicitud
            ->orderBy('fecha_creacion', 'DESC') // Ordena por fecha_creacion en orden descendente
            ->first(); // Devuelve el primer registro de los resultados
    }

    public function getDatosCobrosC($numeroSolicitud)
    {
        $sql = "
        SELECT c.nombre_completo, f.no_factura,c.telefono, c.direccion, c.correo, s.detalle_series
        FROM clientes c
        INNER JOIN solicitud s ON c.id_cliente = s.id_cliente
        INNER JOIN cobros co ON s.id_solicitud = co.id_solicitud
        INNER JOIN historial_cobros hc ON co.id_cobro = hc.id_cobro
        INNER JOIN facturas f ON hc.id_factura = f.id_factura
        WHERE s.id_solicitud = ?
        ORDER BY f.id_factura DESC
        LIMIT 1;
    ";

        // Ejecutar la consulta y devolver el resultado
        return $this->db->query($sql, [$numeroSolicitud])->getResultArray();
    }

    public function esPrimaPendiente($numeroSolicitud)
    {
        $sql = "
        SELECT c.esPrima, c.estado
        FROM cobros c
        INNER JOIN solicitud s 
            ON c.id_solicitud = s.id_solicitud
        WHERE s.id_solicitud = ?
          AND c.esPrima = 1
        ORDER BY c.id_cobro DESC
        LIMIT 1
    ";

        $result = $this->db->query($sql, [$numeroSolicitud])->getRow();

        log_message('info', 'Resultado de esPrimaPendiente para solicitud ' . $numeroSolicitud . ': ' . print_r($result, true));
        if ($result) {
            if ($result->estado === 'PENDIENTE') {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }



    /* public function obtenerPagosRealizados($numeroSolicitud, $id_sucursal, $idSolicitud)
    {
        try {
            // Construimos la consulta SQL personalizada
            // antes estaba asi WHERE s.numero_solicitud = ? AND s.id_sucursal = ?, solo se necesita la soli
            $sql = "SELECT 
                        hc.*, 
                        s.monto_solicitud, c.numero_cuota, 
                        DATE_FORMAT(f.fecha_creacion, '%d/%m/%Y a las %H:%i') AS fecha_formateada,
                        DATE_FORMAT(c.fecha_vencimiento, '%d/%m/%Y') AS fecha_vence, c.interesGenerado,c.estado, c.monto_cuota,
                        s.monto_solicitud - (
                            SELECT IFNULL(SUM(hc2.abono), 0) 
                            FROM historial_cobros hc2
                            INNER JOIN cobros c2 ON hc2.id_cobro = c2.id_cobro
                            WHERE c2.id_solicitud = s.id_solicitud 
                            AND hc2.id_historial_cobro <= hc.id_historial_cobro
                        ) AS saldo_restante
                    FROM 
                        solicitud s 
                        INNER JOIN cobros c ON s.id_solicitud = c.id_solicitud 
                        LEFT JOIN historial_cobros hc ON hc.id_cobro = c.id_cobro
                        LEFT JOIN facturas f ON hc.id_factura = f.id_factura
                    WHERE 
                        s.numero_solicitud = ? 
                        AND s.id_solicitud = ?
                    ORDER BY 
                        c.numero_cuota";

            // Ejecutamos la consulta con el número de solicitud como parámetro
            //$query = $this->db->query($sql, [$numeroSolicitud, $id_sucursal]);
            //$query = $this->db->query($sql, [$numeroSolicitud]);
            $query = $this->db->query($sql, [$numeroSolicitud, $idSolicitud]);

            // Retornamos el resultado como un array de objetos
            return $query->getResult();
        } catch (\Exception $e) {
            // Si ocurre un error, retornar un array vacío
            return [];
        }
    } */

    public function obtenerPagosRealizados($numeroSolicitud, $id_sucursal, $idSolicitud)
    {
        try {
            $sql = "SELECT 
                    hc.*, 
                    s.monto_solicitud, 
                    c.numero_cuota, 
                    DATE_FORMAT(f.fecha_creacion, '%d/%m/%Y a las %H:%i') AS fecha_formateada,
                    DATE_FORMAT(c.fecha_vencimiento, '%d/%m/%Y') AS fecha_vence, 
                    c.interesGenerado,
                    c.estado, 
                    c.monto_cuota,
                    CASE 
                        WHEN hc.id_sucursal_proceso != 0 THEN UPPER(suc.codigo_sucursal)
                        ELSE ''
                    END AS sucursal_proceso_codigo,
                    s.monto_solicitud - (
                        SELECT IFNULL(SUM(hc2.abono), 0) 
                        FROM historial_cobros hc2
                        INNER JOIN cobros c2 ON hc2.id_cobro = c2.id_cobro
                        WHERE c2.id_solicitud = s.id_solicitud 
                        AND hc2.id_historial_cobro <= hc.id_historial_cobro
                    ) AS saldo_restante
                FROM 
                    solicitud s 
                    INNER JOIN cobros c ON s.id_solicitud = c.id_solicitud 
                    LEFT JOIN historial_cobros hc ON hc.id_cobro = c.id_cobro
                    LEFT JOIN facturas f ON hc.id_factura = f.id_factura
                    LEFT JOIN sucursal suc ON hc.id_sucursal_proceso = suc.id_sucursal
                WHERE 
                    s.numero_solicitud = ? 
                    AND s.id_solicitud = ?
                ORDER BY 
                    c.numero_cuota";

            $query = $this->db->query($sql, [$numeroSolicitud, $idSolicitud]);

            return $query->getResult();
        } catch (\Exception $e) {
            return [];
        }
    }


    public function obtenerPagosRealizadosSolV($numeroSolicitud, $id_sucursal, $idSolicitud)
    {
        try {
            // Construimos la consulta SQL personalizada
            // antes estaba asi WHERE s.numero_solicitud = ? AND s.id_sucursal = ?, solo se necesita la soli
            $sql = "SELECT 
                    c.numero_cuota,
                    c.descripcion, c.interesGenerado,c.estado, c.monto_cuota,
                    c.cantAbono AS abono,
                    DATE_FORMAT(c.fecha_pago, '%d/%m/%Y a las %H:%i') AS fecha_formateada,
                    DATE_FORMAT(c.fecha_vencimiento, '%d/%m/%Y') AS fecha_vence,
                    s.monto_solicitud - (
                        SELECT IFNULL(SUM(c2.cantAbono), 0)
                            FROM cobros c2
                            WHERE c2.id_solicitud = s.id_solicitud
                            AND (
                                c2.fecha_pago < c.fecha_pago
                                OR (c2.fecha_pago = c.fecha_pago AND c2.id_cobro <= c.id_cobro)
                                OR (c.estado = 'PENDIENTE')
                            )
                        ) AS saldo_restante
                FROM cobros c 
                INNER JOIN solicitud s ON c.id_solicitud = s.id_solicitud
                WHERE s.numero_solicitud = ? AND s.numero_solicitud LIKE 'V-%' AND s.id_solicitud = ?
                ORDER BY numero_cuota ASC";

            // Ejecutamos la consulta con el número de solicitud como parámetro
            //$query = $this->db->query($sql, [$numeroSolicitud, $id_sucursal]);
            //$query = $this->db->query($sql, [$numeroSolicitud]);
            $query = $this->db->query($sql, [$numeroSolicitud, $idSolicitud]);
            // Retornamos el resultado como un array de objetos
            return $query->getResult();
        } catch (\Exception $e) {
            // Si ocurre un error, retornar un array vacío
            return [];
        }
    }

    public function obtenerCliente($numeroSolicitud, $id_sucursal, $idSolicitud)
    {
        try {
            // Construimos la consulta SQL personalizada para obtener la información del cliente
            //antes estaba asi  WHERE s.numero_solicitud = ? AND s.id_sucursal = ?"; solo se necesita la soli
            $sql = "SELECT c.dui, 
                       c.nombre_completo, 
                       c.telefono,  
                       CONCAT(c.direccion, ', ', col.nombre, ', Distrito de ', dis.nombre, ', Municipio de ', m.nombre, ', Departamento de ', d.nombre) AS direccion_completa
                FROM solicitud s 
                INNER JOIN clientes c ON s.id_cliente = c.id_cliente
                INNER JOIN departamentos d ON c.departamento = d.id
                INNER JOIN municipios m ON c.municipio = m.id
                INNER JOIN distritos dis ON c.distrito = dis.id_distrito 
                INNER JOIN colonias col ON c.colonia = col.id
                WHERE s.numero_solicitud = ?  and s.id_solicitud = ?";

            // Ejecutamos la consulta con el número de solicitud como parámetro
            //$query = $this->db->query($sql, [$numeroSolicitud, $id_sucursal]);
            $query = $this->db->query($sql, [$numeroSolicitud, $idSolicitud]);
            // Retornamos el resultado como un array de objetos
            return $query->getResult();
        } catch (\Exception $e) {
            // Si ocurre un error, retornar un array vacío
            return [];
        }
    }

    public function obtenerInfoSolicitud($numeroSolicitud, $id_sucursal, $idSolicitud)
    {
        try {
            // Construimos la consulta SQL para obtener la información del plan de pago
            // WHERE s.numero_solicitud = ? AND s.id_sucursal = ? "; solo la soli
            $sql = "SELECT pg.*, 
                        s.numero_solicitud,
                        s.monto_solicitud,
                        GROUP_CONCAT(DISTINCT p.codigo_producto SEPARATOR ', ') as codigos_productos,
                        s.id_sucursal
                    FROM solicitud s 
                    LEFT JOIN plan_de_pago pg ON s.id_solicitud = pg.id_solicitud
                    LEFT JOIN productos_solicitud ps ON ps.id_solicitud = s.id_solicitud
                    LEFT JOIN productos p ON p.id_producto = ps.id_producto
                    WHERE s.numero_solicitud = ? and s.id_solicitud = ?
                    GROUP BY s.id_solicitud";

            // Ejecutamos la consulta con el número de solicitud como parámetro
            //$query = $this->db->query($sql, [$numeroSolicitud, $id_sucursal]);
            $query = $this->db->query($sql, [$numeroSolicitud, $idSolicitud]);

            // Retornamos el resultado como un array de objetos
            return $query->getResult();
        } catch (\Exception $e) {
            // Si ocurre un error, retornar un array vacío
            return [];
        }
    }

    public function obtenerSolicitudesPorCliente($idCliente)
    {
        $builder = $this->where('id_cliente', $idCliente)
            ->select('numero_solicitud, fecha_creacion, montoApagar');

        return $builder->findAll();
    }

    public function actualizarMontosPorId($idSolicitud, $saldoApagar)
    {
        try {
            if (empty($idSolicitud) || !is_numeric($saldoApagar)) {
                log_message('error', '❌ ID de solicitud vacío o monto no válido. ID: ' . print_r($idSolicitud, true) . ', Monto: ' . print_r($saldoApagar, true));
                return false;
            }

            $data = [
                'monto_solicitud' => $saldoApagar,
                'montoApagar' => $saldoApagar
            ];

            $resultado = $this->update($idSolicitud, $data);

            log_message('info', '✅ Actualización realizada para ID ' . $idSolicitud . ' con datos: ' . json_encode($data));

            return $resultado;
        } catch (\Throwable $th) {
            log_message('critical', '❗ Error al actualizar solicitud ID ' . $idSolicitud . ': ' . $th->getMessage());
            return false;
        }
    }

    public function actualizarDetalleSeries($idSolicitud, $detalleSeries)
    {
        try {
            if (empty($idSolicitud)) {
                log_message('error', '❌ ID de solicitud vacío. ID: ' . print_r($idSolicitud, true));
                return false;
            }

            $data = [
                'detalle_series' => $detalleSeries
            ];

            $resultado = $this->update($idSolicitud, $data);

            log_message('info', '✅ Actualización realizada para ID ' . $idSolicitud . ' con detalle_series: ' . json_encode($detalleSeries));

            return $resultado;
        } catch (\Throwable $th) {
            log_message('critical', '❗ Error al actualizar detalle_series para solicitud ID ' . $idSolicitud . ': ' . $th->getMessage());
            return false;
        }
    }

    public function eliminarMovimientosPorSolicitud($id_solicitud)
    {
        $this->db
            ->table('movimientos')
            ->where('id_solicitud', $id_solicitud)
            ->delete();

        return $this->db->affectedRows(); // número de filas afectadas
    }
}
