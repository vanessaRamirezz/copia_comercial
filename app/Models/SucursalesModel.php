<?php

namespace App\Models;
use CodeIgniter\Model;
class SucursalesModel extends Model{

    protected $table = 'sucursal';
    protected $primaryKey = 'id_sucursal';

    public function getSucursales($sucursal){
        $this->select('*');
        $this->where('id_sucursal',$sucursal);
        return $this->findAll();
    }

    public function getSucursalesAll(){
        return $this->findAll();
    }
}