<?php

namespace App\Models;

use CodeIgniter\Model;

class PerfilesModel extends Model
{

    protected $table = 'perfiles';
    protected $primaryKey = 'id_perfil';

    public function getPerfiles($perfil)
    {
        try {
            // Seleccionar todos los perfiles excepto el perfil dado y el perfil 1
            $this->select('*');
            $this->whereNotIn('id_perfil', array($perfil, 1));
            return $this->findAll();
        } catch (\Exception $e) {
            log_message('error', $e->getMessage());
            return false;
        }
    }

    public function getAccesosXPerfiles($idPerfil)
    {
        try {
            $this->distinct();
            $this->select("pa.id_perfiles_accesos, a.id_acceso ,a.acceso");
            $this->from('perfiles as p');
            $this->join('perfiles_accesos as pa', 'p.id_perfil = pa.id_perfil');
            $this->join('accesos as a', 'a.id_acceso = pa.id_acceso');
            //$this->where('a.ver_en_menu', 1);
            $this->where('p.id_perfil', $idPerfil);
            return $this->findAll();
        } catch (\Throwable $e) {
            log_message('error', "Error getPerfilesPermisos" . $e->getMessage());
            return false;
        }
    }

    public function accesosAll()
    {
        try {
            // Seleccionar todos los accesos desde la tabla 'accesos'
            $builder = $this->db->table('accesos');
            $builder->select('*');
            return $builder->get()->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', "Error en accesosAll: " . $e->getMessage());
            return false;
        }
    }

    public function insertAccesoPerfil($idPerfil, $idAcceso) {
        $data = [
            'id_perfil' => $idPerfil,
            'id_acceso' => $idAcceso
        ];

        $this->db->table('perfiles_accesos')->insert($data);

        if ($this->db->affectedRows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteAccesoPerfil($idPerfil, $idAcceso) {
        $this->db->table('perfiles_accesos')
                 ->where('id_perfil', $idPerfil)
                 ->where('id_acceso', $idAcceso)
                 ->delete();
        if ($this->db->affectedRows() > 0) {
            return true;
        } else {
            return false;
        }
    }
}
