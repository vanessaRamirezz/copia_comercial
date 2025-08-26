<?php
namespace App\Models;

use CodeIgniter\Model;

class HistorialCobrosModel extends Model
{
    protected $table = 'historial_cobros';
    protected $primaryKey = 'id_historial_cobro';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = ['abono', 'id_cobro', 'descripcion', 'id_factura', 'id_sucursal_proceso'];
}
