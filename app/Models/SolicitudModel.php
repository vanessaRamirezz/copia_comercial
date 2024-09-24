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
        'observacion'
    ];

    // Configuración para devolver el ID creado después de la inserción
    protected $useAutoIncrement = true;

    public function solicitudPorSucursal($idSucursal){
        return $this->select('s.id_solicitud, s.numero_solicitud, s.fecha_creacion, c.dui, c.nombre_completo, CONCAT(u.nombres, " ", u.apellidos) as user_creador, es.estado, s.id_estado_actual, s.observacion, num_contrato')
        ->from('solicitud as s')
        ->join('clientes as c', 's.id_cliente = c.id_cliente')
        ->join('usuarios as u', 's.id_usuario_creacion = u.id_usuario')
        ->join('estados_solicitud as es', 's.id_estado_actual = es.id_estado')
        ->join('contrato_solicitud as cs', 'cs.numero_solicitud = s.numero_solicitud','left')
        ->where('s.id_sucursal', $idSucursal)
        ->findAll();
    }

}
