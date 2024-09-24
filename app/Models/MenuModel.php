<?php

namespace App\Models;
use CodeIgniter\Model;
class MenuModel extends Model{

    protected $table = 'accesos';
    protected $primaryKey = 'id_accesos';

    public function getMenu($idperfil){
        $this->select('accesos.*');
        $this->join('perfiles_accesos', 'accesos.id_acceso = perfiles_accesos.id_acceso');
        $this->where('accesos.ver_en_menu',1);
        $this->where('perfiles_accesos.id_perfil',$idperfil);
        return $this->findAll();
    }
}