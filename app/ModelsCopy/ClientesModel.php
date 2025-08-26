<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientesModel extends Model
{

    protected $table = 'clientes';
    protected $primaryKey = 'id_cliente';
    protected $allowedFields = [
        'id_cliente',
        'dui',
        'nombre_completo',
        'estado_civil',
        'fecha_nacimiento',
        'telefono',
        'direccion',
        'departamento',
        'municipio',
        'distrito',
        'colonia',
        'correo',
        'nombre_conyugue',
        'direccion_trabajo_conyugue',
        'telefono_trabajo_conyugue',
        'nombre_padres',
        'direccion_padres',
        'telefono_padres',
        'CpropiaCN',
        'CpromesaVentaCN',
        'CalquiladaCN',
        'aQuienPerteneceCN',
        'telPropietarioCN',
        'tiempoDeVivirDomicilioCN',
        'id_user_creacion',
        'id_sucursal_creacion',
        'duiFrontal',
        'duiReversa'
    ];

    public function getClientes()
    {
        return $this->orderBy('id_cliente', 'DESC')->findAll();
    }

    public function obtenerClientePorId($id_cliente)
    {
        return $cliente = $this->where('id_cliente', $id_cliente)->first();
    }


    public function guardarCliente($data)
    {
        // Insertar los datos en la base de datos
        $this->insert($data);

        // Verificar si la inserción fue exitosa
        if ($this->db->affectedRows() > 0) {
            return true; // La inserción fue exitosa
        } else {
            return false; // La inserción falló
        }
    }

    /**
     * Verifica si ya existe un cliente con el mismo DUI.
     *
     * @param string $dui DUI a verificar
     * @return bool True si existe un cliente con el mismo DUI, false si no
     */
    public function existeDui(string $dui): bool
    {
        // Realizar una consulta para verificar si ya existe un cliente con el mismo DUI
        $cliente = $this->where('dui', $dui)->first();

        // Si se encontró un cliente con el mismo DUI, devolver true
        // Si no se encontró ningún cliente con el mismo DUI, devolver false
        return ($cliente !== null);
    }

    /**
     * Verifica si ya existe un cliente con el mismo DUI o ID de cliente.
     *
     * @param string|null $dui DUI a verificar
     * @param int|null $id_cliente ID del cliente a verificar
     * @return array|null El registro del cliente si existe, null si no
     */
    public function buscarCliente(?string $dui = null, ?int $id_cliente = null)
    {
        if ($dui) {
            // Realizar una consulta para verificar si ya existe un cliente con el mismo DUI
            $cliente = $this->where('dui', $dui)->first();
        } elseif ($id_cliente) {
            // Realizar una consulta para verificar si ya existe un cliente con el mismo ID
            $cliente = $this->where('id_cliente', $id_cliente)->first();
        } else {
            // Si no se proporciona ningún parámetro, devolver null
            return null;
        }

        // Devolver el registro del cliente si existe, null si no
        return $cliente;
    }

    public function actualizarCliente($id, $data)
    {
        return $this->update($id, $data);
    }

    public function buscarPorNombreLike($nombre)
    {
        // Limpiar la cadena de búsqueda
        $nombre = trim($nombre);

        // Construir la consulta manualmente para forzar el LOWER()
        $query = $this->db->table('clientes')
            ->where("LOWER(nombre_completo) LIKE", '%' . strtolower($nombre) . '%')
            ->get();

        // Obtener resultados
        $clientes = $query->getResultArray();

        // Log para ver la consulta ejecutada y los resultados
        log_message('info', 'Consulta ejecutada: ' . $this->db->getLastQuery());
        log_message('info', 'Resultados obtenidos: ' . json_encode($clientes));

        return $clientes;
    }

    public function obtenerClientesFiltrados($filtros)
    {
        $builder = $this->builder(); // Es lo mismo que: $this->db->table('clientes');

        // Realiza el inner join con la tabla de solicitud
        $builder->select('clientes.dui, clientes.id_cliente, clientes.nombre_completo, clientes.id_sucursal_creacion, SUM(solicitud.montoApagar) as total_monto');
        $builder->join('solicitud', 'solicitud.id_cliente = clientes.id_cliente', 'inner');

        // Filtro por departamento (solo si viene y no es "-1")
        if (!empty($filtros['departamento']) && $filtros['departamento'] != '-1') {
            $builder->where('clientes.departamento', $filtros['departamento']);
        }

        if (!empty($filtros['municipio']) && $filtros['municipio'] != '-1') {
            $builder->where('clientes.municipio', $filtros['municipio']);
        }

        if (!empty($filtros['distrito']) && $filtros['distrito'] != '-1') {
            $builder->where('clientes.distrito', $filtros['distrito']);
        }

        if (!empty($filtros['colonia']) && $filtros['colonia'] != '-1') {
            $builder->where('clientes.colonia', $filtros['colonia']);
        }

        if (!empty($filtros['sucursal']) && $filtros['sucursal'] != '') {
            $builder->where('clientes.id_sucursal_creacion', $filtros['sucursal']);
        }

        // Filtrar por estado (activo o inactivo) según el monto
        if (!empty($filtros['estado'])) {
            if ($filtros['estado'] === 'Activos') {
                $builder->having('SUM(solicitud.montoApagar) >', 0); // Solo activos con monto > 0
            } elseif ($filtros['estado'] === 'Cancelados') {
                $builder->having('SUM(solicitud.montoApagar) =', 0); // Solo inactivos con monto = 0
            }
        }

        // Agrupar por cliente para obtener el total por cliente
        $builder->groupBy('clientes.id_cliente');

        return $builder->get()->getResultArray();
    }

    public function obtenerMoraClientes($filtros)
    {
        $builder = $this->db->table('clientes c');
        $builder->select("
        c.nombre_completo AS cliente,
        s.numero_solicitud,
        c.telefono,
        (
            SELECT co2.fecha_pago
            FROM cobros co2
            WHERE co2.id_solicitud = s.id_solicitud
              AND co2.estado = 'CANCELADO'
              AND co2.fecha_pago IS NOT NULL
            ORDER BY co2.fecha_pago ASC
            LIMIT 1
        ) AS fecha_compra,
        SUM(CASE WHEN DATEDIFF(CURDATE(), co.fecha_vencimiento) < 0 THEN co.monto_cuota ELSE 0 END) AS sin_vencer,
        SUM(CASE WHEN DATEDIFF(CURDATE(), co.fecha_vencimiento) BETWEEN 1 AND 30 THEN co.monto_cuota ELSE 0 END) AS mora_1_30,
        SUM(CASE WHEN DATEDIFF(CURDATE(), co.fecha_vencimiento) BETWEEN 31 AND 60 THEN co.monto_cuota ELSE 0 END) AS mora_31_60,
        SUM(CASE WHEN DATEDIFF(CURDATE(), co.fecha_vencimiento) BETWEEN 61 AND 90 THEN co.monto_cuota ELSE 0 END) AS mora_61_90,
        SUM(CASE WHEN DATEDIFF(CURDATE(), co.fecha_vencimiento) BETWEEN 91 AND 120 THEN co.monto_cuota ELSE 0 END) AS mora_91_120,
        SUM(CASE WHEN DATEDIFF(CURDATE(), co.fecha_vencimiento) BETWEEN 121 AND 150 THEN co.monto_cuota ELSE 0 END) AS mora_121_150,
        SUM(CASE WHEN DATEDIFF(CURDATE(), co.fecha_vencimiento) > 150 THEN co.monto_cuota ELSE 0 END) AS mora_mas_150,
        SUM(CASE WHEN DATEDIFF(CURDATE(), co.fecha_vencimiento) > 0 THEN co.monto_cuota ELSE 0 END) AS total_mora
    ");

        $builder->join('solicitud s', 'c.id_cliente = s.id_cliente');
        $builder->join('cobros co', 's.id_solicitud = co.id_solicitud');

        $builder->where('s.tipo_solicitud', 'CREDITO');
        $builder->where('s.montoApagar >', 0);
        $builder->whereIn('s.id_estado_actual', [2, 5]);
        $builder->where('co.estado', 'PENDIENTE');

        // Aplicar filtros dinámicos
        if (!empty($filtros['departamento']) && $filtros['departamento'] != '-1') {
            $builder->where('c.departamento', $filtros['departamento']);
        }
        if (!empty($filtros['municipio']) && $filtros['municipio'] != '-1') {
            $builder->where('c.municipio', $filtros['municipio']);
        }
        
        if (!empty($filtros['distrito']) && $filtros['distrito'] != '-1') {
            $builder->where('c.distrito', $filtros['distrito']);
        }
        
        if (!empty($filtros['colonia']) && $filtros['colonia'] != '-1') {
            $builder->where('c.colonia', $filtros['colonia']);
        }        
        if (!empty($filtros['sucursal'])) {
            $builder->where('c.id_sucursal_creacion', $filtros['sucursal']);
        }

        $builder->groupBy('c.nombre_completo, s.numero_solicitud, c.telefono, s.id_solicitud');
        $builder->orderBy('c.nombre_completo, s.numero_solicitud');

        return $builder->get()->getResult();
    }
}
