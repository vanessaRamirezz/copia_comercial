<?php

namespace App\Models;

use CodeIgniter\Model;

class CobrosModel extends Model
{
    protected $table = 'cobros';  // Nombre de la tabla
    protected $primaryKey = 'id_cobro';  // Llave primaria

    // Campos permitidos para insert y update
    protected $allowedFields = [
        'id_solicitud',
        'numero_cuota',
        'monto_cuota',
        'descripcion',
        'estado',
        'fecha_vencimiento',
        'fecha_pago',
        'interesGenerado',
        'esPrima',
        'cantAbono'
    ];

    public function getCobrosBySolicitud($id_solicitud)
    {
        return $this->where('id_solicitud', $id_solicitud)
            ->orderBy('numero_cuota', 'ASC')
            ->findAll();
    }

    public function getCobroById($id_cobro)
    {
        return $this->where('id_cobro', $id_cobro)->first();  // Devuelve el primer registro que coincide con el id_cobro
    }

    public function getCobrosPendientesByNumeroSolicitud($numeroSolicitud)
    {
        $builder = $this->db->table('cobros c');
        $builder->select('c.*,s.id_solicitud');
        $builder->join('solicitud s', 'c.id_solicitud = s.id_solicitud');
        $builder->where('s.numero_solicitud', $numeroSolicitud);
        $builder->where('c.estado', 'PENDIENTE');

        return $builder->get()->getResult(); // Devuelve todos los registros que cumplen con las condiciones
    }

    public function getPrimerCobroNoPrima($id_solicitud)
    {
        return $this->where('id_solicitud', $id_solicitud)
            ->where('esPrima !=', 1)
            ->orderBy('id_cobro', 'ASC') // Puedes cambiar el campo si quieres otro orden
            ->get()
            ->getRow(); // Devuelve solo el primer registro encontrado
    }

    public function getCobrosPendientesByIdSolicitud($id)
    {
        $builder = $this->db->table('cobros c');
        $builder->select('c.*,s.id_solicitud');
        $builder->join('solicitud s', 'c.id_solicitud = s.id_solicitud');
        $builder->where('s.id_solicitud', $id);
        $builder->where('c.estado', 'PENDIENTE');

        return $builder->get()->getResult(); // Devuelve todos los registros que cumplen con las condiciones
    }

    public function actualizarMontoCuotaPorSolicitud($idSolicitud, $saldoApagar)
    {
        try {
            if (empty($idSolicitud) || !is_numeric($saldoApagar)) {
                log_message('error', '❌ ID de solicitud vacío o monto no válido. ID: ' . print_r($idSolicitud, true) . ', Monto: ' . print_r($saldoApagar, true));
                return false;
            }

            $data = ['monto_cuota' => $saldoApagar];

            $resultado = $this->where('id_solicitud', $idSolicitud)->set($data)->update();

            log_message('info', '✅ Cuotas actualizadas para solicitud ID ' . $idSolicitud . ' con monto: ' . $saldoApagar);

            return $resultado;
        } catch (\Throwable $th) {
            log_message('critical', '❗ Error al actualizar monto de cuota: ' . $th->getMessage());
            return false;
        }
    }
}
