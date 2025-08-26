<?php

namespace App\Models;

use CodeIgniter\Model;

class LoginModel extends Model
{

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    protected $allowedFields = ['token', 'dui'];

    public function validateUser($user)
    {
        $this->select('*');
        $this->join('sucursal', 'usuarios.id_sucursal = sucursal.id_sucursal');
        $this->join('perfiles', 'usuarios.id_perfil = perfiles.id_perfil');
        $this->where('usuarios.dui', $user);
        return $this->findAll();
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
