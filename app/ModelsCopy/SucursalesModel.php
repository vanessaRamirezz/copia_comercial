<?php

namespace App\Models;

use CodeIgniter\Model;

class SucursalesModel extends Model
{

    protected $table = 'sucursal';
    protected $primaryKey = 'id_sucursal';
    protected $allowedFields = [
        'id_sucursal',
        'sucursal',
        'codigo_sucursal',
        'id_departamento',
        'id_municipio',
        'id_distrito',
        'id_colonia'
    ];

    public function getSucursales($sucursalId)
    {
        return $this->select('
            sucursal.*,
            departamentos.nombre AS nombre_departamento,
            municipios.nombre AS nombre_municipio,
            distritos.nombre AS nombre_distrito,
            colonias.nombre AS nombre_colonia
        ')
            ->join('departamentos', 'departamentos.id = sucursal.id_departamento')
            ->join('municipios', 'municipios.id = sucursal.id_municipio')
            ->join('distritos', 'distritos.id_distrito = sucursal.id_distrito')
            ->join('colonias', 'colonias.id = sucursal.id_colonia')
            ->where('sucursal.id_sucursal', $sucursalId)
            ->first(); // o ->findAll() si esperas más de una sucursal
    }


    public function getSucursalesAll()
    {
        return $this->findAll();
    }

    public function getSucursalesDescripcion()
    {
        return $this->select('
            sucursal.*,
            departamentos.nombre AS nombre_departamento,
            municipios.nombre AS nombre_municipio,
            distritos.nombre AS nombre_distrito,
            colonias.nombre AS nombre_colonia
        ')
            ->join('departamentos', 'departamentos.id = sucursal.id_departamento')
            ->join('municipios', 'municipios.id = sucursal.id_municipio')
            ->join('distritos', 'distritos.id_distrito = sucursal.id_distrito')
            ->join('colonias', 'colonias.id = sucursal.id_colonia')
            ->findAll(); // o ->findAll() si esperas más de una sucursal
    }
}
