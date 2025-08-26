<?php

namespace App\Controllers;

use App\Models\SolicitudModel;

class EstadoCuentaController extends BaseController
{
    public function estadoDeCuenta()
    {
        $session = session();

        if ($session->get('sesion_activa') === true) {

            $id_sucursal = $session->get('sucursal');
            $estadoDeCuentaModel = new SolicitudModel();
            $numeroSolicitud = $this->request->getPost('noSolicitud');

            if (!$numeroSolicitud) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Número de solicitud no proporcionado.'
                ]);
            }

            $datosPagos = $estadoDeCuentaModel->obtenerPagosRealizados($numeroSolicitud, $id_sucursal);
            if (empty($datosPagos)) {
                $datosPagos = $estadoDeCuentaModel->obtenerPagosRealizadosSolV($numeroSolicitud, $id_sucursal);
            }

            $datosClientes = $estadoDeCuentaModel->obtenerCliente($numeroSolicitud, $id_sucursal);
            $datosSolicitud = $estadoDeCuentaModel->obtenerInfoSolicitud($numeroSolicitud, $id_sucursal);

            if (empty($datosClientes)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'No se encontraron datos para la solicitud proporcionada.'
                ]);
            }

            $db = \Config\Database::connect();

            foreach ($datosSolicitud as &$solicitud) {
                $codigoProductosVacio = empty($solicitud->codigos_productos);
                $esSolicitudV = str_starts_with($solicitud->numero_solicitud, 'V-');

                if ($codigoProductosVacio && $esSolicitudV) {
                    // Paso 1: obtener códigos desde productos_solicitud_anterior
                    $productosAnteriores = $db->table('productos_solicitud_anterior')
                        ->select('articulo_principal, articulos_varios')
                        ->where('id_solicitud', function ($builder) use ($solicitud) {
                            $builder->select('id_solicitud')
                                ->from('solicitud')
                                ->where('numero_solicitud', $solicitud->numero_solicitud)
                                ->limit(1);
                        })
                        ->get()
                        ->getResultArray();

                    $codigos = [];

                    foreach ($productosAnteriores as $producto) {
                        if (!empty($producto['articulo_principal'])) {
                            $codigos[] = $producto['articulo_principal'];
                        }

                        if (!empty($producto['articulos_varios'])) {
                            $varios = explode(',', $producto['articulos_varios']);
                            $codigos = array_merge($codigos, array_map('trim', $varios));
                        }
                    }

                    $codigos = array_unique($codigos);

                    if (!empty($codigos)) {
                        // Paso 2: buscar esos códigos en la tabla productos
                        $detallesProductos = $db->table('productos')
                            ->select('codigo_producto, nombre')
                            ->whereIn('codigo_producto', $codigos)
                            ->get()
                            ->getResultArray();

                        // Podés guardar nombre y descripción en una lista
                        $descripciones = [];
                        foreach ($detallesProductos as $producto) {
                            $descripciones[] = "{$producto['codigo_producto']} - {$producto['nombre']} ";
                        }

                        // Guardar como texto unificado en el campo codigos_productos
                        $solicitud->codigos_productos = implode(', ', $descripciones);
                    }
                }
            }

            // Preparar los datos para la respuesta
            $data = [
                'status' => 'success',
                'message' => 'Estado de cuenta recuperado correctamente.',
                'data' => [
                    'pagos' => $datosPagos,
                    'cliente' => $datosClientes,
                    'solicitud' => $datosSolicitud
                ]
            ];

            return $this->response->setJSON($data);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No hay sesión activa, por favor inicie sesión.'
            ]);
        }
    }
}
