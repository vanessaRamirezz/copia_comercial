<?php

namespace App\Models;

use CodeIgniter\Model;

class FacturasModel extends Model
{
    protected $table = 'facturas';  // Nombre de la tabla
    protected $primaryKey = 'id_factura';  // Llave primaria

    // Campos permitidos para insert y update
    protected $allowedFields = [
        'id_factura',
        'no_factura',
        'id_sucursal',
        'id_usuario',
        'fecha_creacion',
        'ruta_factura'
    ];

    /* public function insertarFactura($idSucursal, $id_usuario)
    {
        $db = \Config\Database::connect();

        // Modificando la consulta SQL para incluir id_usuario
        $query = "INSERT INTO facturas (no_factura, id_rango_factura, id_sucursal, id_usuario)
              SELECT 
                  (SELECT COALESCE(MIN(f.no_factura) + COUNT(f.no_factura), rf.numero_inicio) 
                   FROM facturas f 
                   WHERE f.id_rango_factura = rf.id_rango_factura) AS no_factura,
                  rf.id_rango_factura,
                  rf.id_sucursal,
                  ? AS id_usuario  -- Aquí agregamos el parámetro id_usuario
              FROM rango_factura rf
              WHERE rf.estado = 'Activo' 
              AND rf.id_sucursal = ?
              ORDER BY rf.id_rango_factura ASC
              LIMIT 1;";

        // Ejecutamos la consulta pasando tanto el id_usuario como idSucursal
        $db->query($query, [$id_usuario, $idSucursal]);

        return $db->insertID();
    } */
    public function insertarFactura($idSucursal, $id_usuario)
    {
        $db = \Config\Database::connect();

        try {
            $db->transStart(); // Iniciar la transacción

            // Obtener el siguiente número de factura disponible
            $query = "SELECT rf.id_rango_factura, 
                         COALESCE(MAX(f.no_factura) + 1, rf.numero_inicio) AS siguiente_factura,
                         rf.numero_fin
                  FROM rango_factura rf
                  LEFT JOIN facturas f ON f.id_rango_factura = rf.id_rango_factura
                  WHERE rf.estado = 'Activo' 
                  AND rf.id_sucursal = ?
                  GROUP BY rf.id_rango_factura, rf.numero_inicio, rf.numero_fin
                  ORDER BY rf.id_rango_factura ASC
                  LIMIT 1
                  FOR UPDATE"; // BLOQUEA el registro hasta que la transacción termine

            $resultado = $db->query($query, [$idSucursal])->getRowArray();

            if (!$resultado) {
                $db->transRollback();
                log_message('error', "No hay rangos de facturación activos disponibles. Sucursal: {$idSucursal}");
                return [
                    'success' => false,
                    'message' => 'No hay rangos de facturación activos disponibles.'
                ];
            }

            // Verificar si el siguiente número de factura supera el límite
            if ($resultado['siguiente_factura'] > $resultado['numero_fin']) {
                $db->transRollback();
                log_message('error', "No hay números de factura disponibles en el rango. Sucursal: {$idSucursal}");
                return [
                    'success' => false,
                    'message' => 'No hay números de factura disponibles en el rango.'
                ];
            }

            // Insertar la nueva factura
            $queryInsert = "INSERT INTO facturas (no_factura, id_rango_factura, id_sucursal, id_usuario)
                        VALUES (?, ?, ?, ?)";
            $db->query($queryInsert, [
                $resultado['siguiente_factura'],
                $resultado['id_rango_factura'],
                $idSucursal,
                $id_usuario
            ]);

            // Obtener el ID de la factura recién insertada
            $id_factura = $db->insertID();

            // Si el número insertado es el último del rango, marcar el rango como "Finalizado"
            if ($resultado['siguiente_factura'] == $resultado['numero_fin']) {
                $queryUpdate = "UPDATE rango_factura SET estado = 'Finalizado' WHERE id_rango_factura = ?";
                $db->query($queryUpdate, [$resultado['id_rango_factura']]);
            }

            $db->transComplete(); // Confirmar la transacción

            if ($db->transStatus() === false) {
                log_message('error', "Error al insertar la factura. Sucursal: {$idSucursal}, Usuario: {$id_usuario}");
                return [
                    'success' => false,
                    'message' => 'Error al insertar la factura.'
                ];
            }

            log_message('info', "Factura insertada correctamente. ID: {$id_factura}, No: {$resultado['siguiente_factura']}, Sucursal: {$idSucursal}, Usuario: {$id_usuario}");

            return [
                'success' => true,
                'message' => 'Factura insertada correctamente.',
                'no_factura' => $resultado['siguiente_factura'],
                'id_factura' => $id_factura
            ];
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', "Excepción al insertar factura: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error inesperado al insertar la factura.'
            ];
        }
    }




    public function obtenerHistorialCobros($idFactCreada)
    {
        return $this->select('hc.descripcion, hc.abono')
            ->join('historial_cobros hc', 'facturas.id_factura = hc.id_factura')
            ->where('facturas.id_factura', $idFactCreada)
            ->findAll();
    }
}
