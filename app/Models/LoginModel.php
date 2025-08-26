<?php

namespace App\Models;

use CodeIgniter\Model;

class LoginModel extends Model
{

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    protected $allowedFields = ['token', 'dui','activo'];

    /* public function validateUser($user)
    {
        $this->select('*');
        $this->join('sucursal', 'usuarios.id_sucursal = sucursal.id_sucursal');
        $this->join('perfiles', 'usuarios.id_perfil = perfiles.id_perfil');
        $this->where('usuarios.dui', $user);
        return $this->findAll();
    } */

    public function validateUser($user)
{
    return $this->select([
            '*',
            'CONCAT(
                sucursal.sucursal, ", ",
                "departamento de ", d.nombre, ", ",
                "del municipio de ", m.nombre, ", ",
                "distrito de ", ds.nombre, ", ",
                "colonia ", c.nombre
            ) AS direccion_completa'
        ])
        ->join('sucursal', 'usuarios.id_sucursal = sucursal.id_sucursal')
        ->join('perfiles', 'usuarios.id_perfil = perfiles.id_perfil')
        ->join('departamentos d', 'd.id = sucursal.id_departamento')
        ->join('municipios m', 'm.id = sucursal.id_municipio')
        ->join('distritos ds', 'ds.id_distrito = sucursal.id_distrito')
        ->join('colonias c', 'c.id = sucursal.id_colonia')
        ->where('usuarios.dui', $user)
        ->findAll();
}


    public function getAccesosUsuario($user)
    {
        $this->select('accesos.url_acceso,accesos.orden_acceso');
        $this->join('perfiles', 'usuarios.id_perfil = perfiles.id_perfil');
        $this->join('perfiles_accesos', 'perfiles.id_perfil = perfiles_accesos.id_perfil');
        $this->join('accesos', 'perfiles_accesos.id_acceso = accesos.id_acceso');
        $this->where('usuarios.dui', $user);
        return $this->findAll();
    }

    public function updateToken($dui, $newToken) {
        // Establecer los datos a actualizar
        $data = [
            'token' => $newToken
        ];
    
        // Realizar la actualización y comprobar el resultado
        $this->where('dui', $dui)
             ->set($data)
             ->update();
    
        // Verificar si la actualización fue exitosa
        if ($this->db->affectedRows() > 0) {
            return true;
        } else {
            return false;
        }
    }
    

    // Método para generar un token único
    public function generateUniqueToken()
    {
        do {
            // Genera un token aleatorio
            $token = bin2hex(random_bytes(4));
        } while ($this->where('token', $token)->first()); // Comprueba si el token ya existe

        return $token;
    }
}
