<?php
namespace App\Models;
use CodeIgniter\Model;
class ProveedoresModel extends Model
{
    protected $table = 'proveedores';
    protected $primaryKey = 'id_proveedor';
    protected $allowedFields = ['nombre','contacto','telefono','email','direccion','estado'];

    public function getProveedores()
    {
        return $this->orderBy('id_proveedor', 'DESC')->findAll();
    }

    public function getProveedoresActivos()
    {
        return $this->where('estado', 1)->orderBy('id_proveedor', 'DESC')->findAll();
    }

    public function actualizarDataProveedor($data,$id_proveedor){
        $this->set('nombre', $data['nombre']);
        $this->set('contacto', $data['contacto']);
        $this->set('telefono', $data['telefono']);
        $this->set('email', $data['email']);
        $this->set('direccion', $data['direccion']);
        $this->set('estado', $data['estado']);
        $this->where('id_proveedor', $id_proveedor);
        return $this->update();;
    }
}