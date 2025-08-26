<?php

namespace App\Models;
use CodeIgniter\Model;
class AdministracionModel extends Model{

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    protected $allowedFields = ['nombres','apellidos','correo','telefono','contrasena'];

    public function updatePassword($dui, $newPassword){
        $this->set('contrasena', $newPassword);
        $this->where('dui', $dui);
        return $this->update();
    }

    public function updateInfoUsurio($data, $dui){
        $this->set('nombres', $data['nombres']);
        $this->set('apellidos', $data['apellidos']);
        $this->set('correo', $data['correo']);
        $this->set('telefono', $data['telefono']);
        $this->where('dui', $dui);
        return $this->update();
    }
}