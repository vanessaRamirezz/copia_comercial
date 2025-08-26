<?php

namespace App\Models;
use CodeIgniter\Model;

class ComisionesModel extends Model
{
    protected $table = 'comisiones';
    protected $primaryKey = 'idcomisiones';
    protected $allowedFields = ['cantidad_meses', 'valor'];

    public function getComisiones()
    {
        return $this->findAll();
    }
}
