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
        'montoApagar'
    ];

    // Configuración para devolver el ID creado después de la inserción
    protected $useAutoIncrement = true;

    public function solicitudPorSucursalEstadoCreadas($idSucursal)
    {
        return $this->select('DISTINCT(s.id_solicitud), s.id_solicitud, s.numero_solicitud, s.fecha_creacion, c.dui, c.nombre_completo, CONCAT(u.nombres, " ", u.apellidos) as user_creador, es.estado, s.id_estado_actual, s.observacion, num_contrato, s.montoApagar')
            ->from('solicitud as s')
            ->join('clientes as c', 's.id_cliente = c.id_cliente')
            ->join('usuarios as u', 's.id_usuario_creacion = u.id_usuario')
            ->join('estados_solicitud as es', 's.id_estado_actual = es.id_estado')
            ->join('contrato_solicitud as cs', 'cs.numero_solicitud = s.numero_solicitud', 'left')
            ->where('s.id_sucursal', $idSucursal)
            ->where('s.id_estado_actual', 1)
            ->findAll();
    }

    public function solicitudPorSucursalEstadoVarias($idSucursal)
    {
        return $this->select('DISTINCT(s.id_solicitud), s.id_solicitud, s.numero_solicitud, s.fecha_creacion, c.dui, c.nombre_completo, CONCAT(u.nombres, " ", u.apellidos) as user_creador, es.estado, s.id_estado_actual, s.observacion, num_contrato, s.montoApagar')
            ->from('solicitud as s')
            ->join('clientes as c', 's.id_cliente = c.id_cliente')
            ->join('usuarios as u', 's.id_usuario_creacion = u.id_usuario')
            ->join('estados_solicitud as es', 's.id_estado_actual = es.id_estado')
            ->join('contrato_solicitud as cs', 'cs.numero_solicitud = s.numero_solicitud', 'left')
            ->where('s.id_sucursal', $idSucursal)
            ->where('s.id_estado_actual !=', 1)
            ->findAll();
    }


    public function solicitudAprobadasPorCliente($dui)
    {
        return $this->select('DISTINCT(s.id_solicitud), s.id_solicitud, s.numero_solicitud, s.fecha_creacion, c.dui, c.nombre_completo, CONCAT(u.nombres, " ", u.apellidos) as user_creador, es.estado, s.id_estado_actual, s.observacion, num_contrato, s.montoApagar')
            ->from('solicitud as s')
            ->join('clientes as c', 's.id_cliente = c.id_cliente')
            ->join('usuarios as u', 's.id_usuario_creacion = u.id_usuario')
            ->join('estados_solicitud as es', 's.id_estado_actual = es.id_estado')
            ->join('contrato_solicitud as cs', 'cs.numero_solicitud = s.numero_solicitud', 'left')
            ->where('c.dui', $dui)
            ->whereIn('s.id_estado_actual', [2, 5])
            ->findAll();
    }

    public function getSolicitud($numeroSolicitud)
    {
        return $this->select('*') // Selecciona todos los campos
            ->where('numero_solicitud', $numeroSolicitud) // Condición por número de solicitud
            ->first(); // Devuelve un solo registro
    }

    /* public function getDatosCobrosC($numeroSolicitud)
    {
        return $this->select('cs.numero_solicitud, c.nombre_completo, co.fecha_vencimiento')
            ->from('solicitud as s')
            ->join('contrato_solicitud as cs', 's.numero_solicitud = cs.numero_solicitud')
            ->join('clientes as c', 's.id_cliente = c.id_cliente')
            ->join('cobros as co', 's.id_solicitud = co.id_solicitud')
            ->where('s.numero_solicitud', $numeroSolicitud)
            ->findAll();
    } */

    public function getDatosCobrosC($numeroSolicitud)
    {
        $sql = "
            SELECT cs.num_contrato, c.nombre_completo
            FROM solicitud as s 
            INNER JOIN contrato_solicitud as cs ON
            s.numero_solicitud = cs.numero_solicitud
            INNER JOIN clientes as c ON
            s.id_cliente = c.id_cliente
            WHERE s.numero_solicitud = ?
        ";
        return $this->db->query($sql, [$numeroSolicitud])->getResultArray();
    }
}