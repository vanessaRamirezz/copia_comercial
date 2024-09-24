<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientesModel extends Model
{

    protected $table = 'clientes';
    protected $primaryKey = 'id_cliente';
    protected $allowedFields = ['id_cliente','dui', 'nombre_completo', 'estado_civil', 'fecha_nacimiento', 'telefono', 'direccion', 'departamento', 'municipio', 'correo', 'nombre_conyugue', 'direccion_trabajo_conyugue', 'telefono_trabajo_conyugue', 'nombre_padres', 'direccion_padres', 'telefono_padres', 'CpropiaCN', 'CpromesaVentaCN', 'CalquiladaCN', 'aQuienPerteneceCN', 'telPropietarioCN', 'tiempoDeVivirDomicilioCN', 'id_user_creacion', 'id_sucursal_creacion'];

    public function getClientes()
    {
        return $this->orderBy('id_cliente', 'DESC')->findAll();
    }


    public function guardarCliente($data)
    {
        // Insertar los datos en la base de datos
        $this->insert($data);

        // Verificar si la inserción fue exitosa
        if ($this->db->affectedRows() > 0) {
            return true; // La inserción fue exitosa
        } else {
            return false; // La inserción falló
        }
    }

    /**
     * Verifica si ya existe un cliente con el mismo DUI.
     *
     * @param string $dui DUI a verificar
     * @return bool True si existe un cliente con el mismo DUI, false si no
     */
    public function existeDui(string $dui): bool
    {
        // Realizar una consulta para verificar si ya existe un cliente con el mismo DUI
        $cliente = $this->where('dui', $dui)->first();

        // Si se encontró un cliente con el mismo DUI, devolver true
        // Si no se encontró ningún cliente con el mismo DUI, devolver false
        return ($cliente !== null);
    }

    /**
     * Verifica si ya existe un cliente con el mismo DUI o ID de cliente.
     *
     * @param string|null $dui DUI a verificar
     * @param int|null $id_cliente ID del cliente a verificar
     * @return array|null El registro del cliente si existe, null si no
     */
    public function buscarCliente(?string $dui = null, ?int $id_cliente = null)
    {
        if ($dui) {
            // Realizar una consulta para verificar si ya existe un cliente con el mismo DUI
            $cliente = $this->where('dui', $dui)->first();
        } elseif ($id_cliente) {
            // Realizar una consulta para verificar si ya existe un cliente con el mismo ID
            $cliente = $this->where('id_cliente', $id_cliente)->first();
        } else {
            // Si no se proporciona ningún parámetro, devolver null
            return null;
        }

        // Devolver el registro del cliente si existe, null si no
        return $cliente;
    }
}
