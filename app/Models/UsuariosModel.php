<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuariosModel extends Model
{
    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    protected $allowedFields = ['nombres', 'apellidos', 'dui', 'correo', 'telefono', 'contrasena', 'id_perfil', 'id_sucursal', 'token', 'activo'];

    public function getUsuarioXSucursal($id_sucursal, $dui)
    {

        $builder = $this->db->table($this->table);
        $builder->select('*');
        $builder->join('perfiles', 'usuarios.id_perfil = perfiles.id_perfil');
        $builder->where('usuarios.id_sucursal', $id_sucursal);
        $builder->where('usuarios.dui !=', $dui);
        $query = $builder->get();
        return $query->getResult();
    }

    public function getUsuarioXSucursalAll($id_sucursal)
    {

        $builder = $this->db->table($this->table);
        $builder->select('*');
        $builder->join('perfiles', 'usuarios.id_perfil = perfiles.id_perfil');
        $builder->where('usuarios.id_sucursal', $id_sucursal);
        $query = $builder->get();
        return $query->getResult();
    }

    public function actualizarDataUsuario($data, $dui)
    {
        $this->set('nombres', $data['nombres']);
        $this->set('apellidos', $data['apellidos']);
        $this->set('correo', $data['correo']);
        $this->set('telefono', $data['telefono']);
        $this->set('id_perfil', $data['id_perfil']);
        $this->set('id_sucursal', $data['id_sucursal']);
        $this->where('dui', $dui);
        return $this->update();;
    }

    public function eliminarUsuarioPorDui($dui)
    {
        $this->where('dui', $dui);
        return $this->delete();
    }

    public function getUsuarios()
    {
        return $this->findAll();
    }

    public function getUsuarioPorDui($dui)
    {
        return $this->where('dui', $dui)->first();
    }
    public function updatePWD($dui, $data)
    {
        return $this->where('dui', $dui)->set($data)->update();
    }

    public function updateEstado($dui, $data)
    {
        return $this->set($data)->where('dui', $dui)->update();
    }
}
