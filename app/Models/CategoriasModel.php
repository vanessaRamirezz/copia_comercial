<?php
namespace App\Models;
use CodeIgniter\Model;
class CategoriasModel extends Model
{
    protected $table = 'categorias';
    protected $primaryKey = 'id_categoria';
    protected $allowedFields = ['nombre','estado'];

    public function getCategoriasActivas()
    {
        return $this->where('estado', 1)->orderBy('id_categoria', 'DESC')->findAll();
    }

    public function getCategorias()
    {
        return $this->orderBy('id_categoria', 'DESC')->findAll();
    }

    public function actualizarDataCategoria($data,$id_categoria){
        $this->set('nombre', $data['nombre']);
        $this->set('estado', $data['estado']);
        $this->where('id_categoria', $id_categoria);
        return $this->update();;
    }
}