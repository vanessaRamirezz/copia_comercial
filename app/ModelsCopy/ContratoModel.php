<?php

namespace App\Models;

use CodeIgniter\Model;

class ContratoModel extends Model
{
    protected $table = 'contrato_solicitud';
    protected $primaryKey = 'id_contrato';

    // Definir campos que se pueden insertar o actualizar
    protected $allowedFields = ['id_sucursal', 'num_contrato'];

    // Método para obtener el código de la sucursal
    public function getCodigoSucursal($id_sucursal)
    {
        return $this->db->table('sucursal')
            ->select('codigo_sucursal')
            ->where('id_sucursal', $id_sucursal)
            ->get()
            ->getRow();
    }

    // Método para obtener el último número de contrato
    public function getUltimoNumeroContrato($id_sucursal)
    {
        return $this->db->table($this->table)
            ->select('IFNULL(MAX(CAST(SUBSTRING_INDEX(num_contrato, "-", -1) AS UNSIGNED)), 0) AS ultimo_num_contrato')
            ->where('id_sucursal', $id_sucursal)
            ->get()
            ->getRow();
    }

    // Método para insertar en la tabla de logs
    public function insertarLog($message)
    {
        $this->db->table('trigger_log')->insert([
            'message' => $message
        ]);
    }

    // Método para obtener un contrato por id_solicitud
    public function getContratoPorSolicitud($id_solicitud)
    {
        return $this->where('id_solicitud', $id_solicitud)
            ->first(); // Retorna la primera coincidencia o null si no hay ninguna
    }
}
