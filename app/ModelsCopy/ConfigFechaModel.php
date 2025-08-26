<?php

namespace App\Models;

use CodeIgniter\Model;

class ConfigFechaModel extends Model
{
    protected $table            = 'control_fecha_registro';
    protected $primaryKey       = 'id';

    protected $allowedFields    = [
        'fecha_virtual',
        'estado'
    ];
}
