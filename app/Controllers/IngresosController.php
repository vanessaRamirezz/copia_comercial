<?php

namespace App\Controllers;

use App\Controllers\ProveedorController;
use App\Models\DocumentosModel;
use App\Models\MovimientosModel;
use App\Models\SucursalesModel;
use App\Models\TiposMovimientosModel;
use TCPDF;
use Dompdf\Dompdf;
use App\Models\ConfigFechaModel;

class IngresosController extends BaseController
{
    private $nameClass = "IngresosController";

    public function index()
    {
        $session = session();
        if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
            $url = $this->request->uri->getSegment(1);
            log_message("info", $this->nameClass . " " . $url);
            $accesos = json_decode($_SESSION['accesos'], true);
            $allowedUrls = array_column($accesos, 'url_acceso');
            if (in_array($url, $allowedUrls)) {
                $proveedoresController = new ProveedorController();
                $sucursalesModel = new SucursalesModel();
                $tiposMovimientosModel = new TiposMovimientosModel();
                $tiposMovimientosModel = new TiposMovimientosModel();

                $fechaModel = new ConfigFechaModel();
                $fechaActiva = $fechaModel->obtenerActivosXSucursal($_SESSION['sucursal']);

                $fechaVirtual = null;
                if (!empty($fechaActiva)) {
                    $fechaVirtual = $fechaActiva[0]['fecha_virtual'];  // tomar solo la fecha virtual del primer registro
                }

                log_message('info', 'El valor de la fecha virtual es: ' . $fechaVirtual);


                $proveedoresActivos = $proveedoresController->getProveedoresAllActives();
                $sucursales = $sucursalesModel->getSucursalesAll();
                $tiposMovimientos = $tiposMovimientosModel->findAll();

                $data = [
                    'proveedoresActivos' => $proveedoresActivos,
                    'sucursales' => $sucursales,
                    'tiposMovimientos' => $tiposMovimientos,
                    'tiposMovimientosM' => $tiposMovimientos,
                    'fechaVirtual' => $fechaVirtual,
                    'idSucursalActual' => $_SESSION['sucursal']
                ];


                $content4 = view('movimientos/formato_ingreso', $data);
                $fullPage = $this->renderPage($content4);
                return $fullPage;
            } else {
                $content4 = view('errors/html/error_403');
                $fullPage = $this->renderPage($content4);
                return $fullPage;
            }
        } else {
            return redirect()->to(base_url());
        }
    }

    public function salidaNotaRemision()
    {
        $session = session();
        if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
            $url = $this->request->uri->getSegment(1);
            log_message("info", $this->nameClass . " " . $url);
            $accesos = json_decode($_SESSION['accesos'], true);
            $allowedUrls = array_column($accesos, 'url_acceso');
            if (in_array($url, $allowedUrls)) {
                $proveedoresController = new ProveedorController();
                $sucursalesModel = new SucursalesModel();
                $tiposMovimientosModel = new TiposMovimientosModel();

                $fechaModel = new ConfigFechaModel();
                $fechaActiva = $fechaModel->obtenerActivosXSucursal($_SESSION['sucursal']);

                $fechaVirtual = null;
                if (!empty($fechaActiva)) {
                    $fechaVirtual = $fechaActiva[0]['fecha_virtual'];  // tomar solo la fecha virtual del primer registro
                }

                log_message('info', 'El valor de la fecha virtual es: ' . $fechaVirtual);

                $proveedoresActivos = $proveedoresController->getProveedoresAllActives();
                $sucursales = $sucursalesModel->getSucursalesAll();
                $tiposMovimientos = $tiposMovimientosModel->findAll();

                $data = [
                    'proveedoresActivos' => $proveedoresActivos,
                    'sucursales' => $sucursales,
                    'tiposMovimientos' => $tiposMovimientos,
                    'tiposMovimientosM' => $tiposMovimientos,
                    'fechaVirtual' => $fechaVirtual,
                    'idSucursalActual' => $_SESSION['sucursal']
                ];


                $content4 = view('movimientos/formato_salida_nrm', $data);
                $fullPage = $this->renderPage($content4);
                return $fullPage;
            } else {
                $content4 = view('errors/html/error_403');
                $fullPage = $this->renderPage($content4);
                return $fullPage;
            }
        } else {
            return redirect()->to(base_url());
        }
    }

    public function ingresar_movimiento()
    {
        try {
            $session = session();
            if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
                log_message("info", $this->nameClass . " ingresar_movimiento");

                $inputJSON = $this->request->getBody();

                $input = json_decode($inputJSON, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception('Error al decodificar JSON: ' . json_last_error_msg());
                }
                $flagSave = false;
                $tipo_Movimiento = $input['tipo_Movimiento'];


                $documentosModel = new DocumentosModel();
                $movimientosModel = new MovimientosModel();
                $descripcionMovimiento = $movimientosModel->getDescripcionById($tipo_Movimiento);

                if ($tipo_Movimiento === '2') {
                    log_message("info", $this->nameClass . " ingresar_movimiento nota de remision SOLO salida (esperando aceptación)");

                    $idUsuarioCreacion = $_SESSION['id_usuario'];
                    $correlativo = $input['correlativo'];
                    $noDocumento = $input['noDocumento'];
                    $sucursal_destino = $input['sucursal_destino'];
                    $observacion = 'Salida por nota de remisión: ' . $input['observacion'];
                    $estado = 'Procesado'; // nuevo estado
                    $sucursal_origen = $input['sucursal_origen'];
                    $proveedor = $input['proveedor'];
                    $productos = $input['productos'];

                    $movimientosModel = new MovimientosModel();
                    $noCorrelativo = $documentosModel->obtenerCorrelativoAutomatico($sucursal_origen, $tipo_Movimiento);

                    if ($noCorrelativo !== null) {
                        $correlativo = $noCorrelativo;
                    }

                    $dataDocumentoSalida = [
                        'id_usuario_creacion' => $idUsuarioCreacion,
                        'correlativo' => $correlativo,
                        'noDocumento' => $noDocumento,
                        'estado' => $estado,
                        'id_proveedor' => $proveedor,
                        'id_sucursal_origen' => $sucursal_origen,
                        'id_sucursal_destino' => $sucursal_destino,
                        'observaciones' => $observacion,
                        'id_tipo_movimiento' => 6, // tipo salida
                    ];

                    $id_documento_salida = $documentosModel->insertDocumento($dataDocumentoSalida);

                    if (!empty($id_documento_salida)) {
                        $flagSave = $this->guardarProductos($productos, $id_documento_salida, 6, $sucursal_origen);
                    }
                } else {
                    log_message("info", " ingresar_movimiento no es nota de remision flujo de entrada");
                    $idUsuarioCreacion = $_SESSION['id_usuario'];
                    $correlativo = $input['correlativo'];
                    $noDocumento = $input['noDocumento'];
                    $sucursal_destino = isset($input['sucursal_destino']) ? $input['sucursal_destino'] : null;
                    $sucursal_origen = isset($input['sucursal_origen']) ? $input['sucursal_origen'] : null;
                    $observacion = $input['observacion'];
                    $productos = $input['productos'];
                    //$estado = $input['estado'];
                    $estado = ($input['estado'] === 'Procesado') ? 'Aceptado' : $input['estado'];
                    $total = isset($input['total']) ? $input['total'] : 0.00;
                    $proveedor = isset($input['proveedor']) ? $input['proveedor'] : null;
                    $tipo_Movimiento = $input['tipo_Movimiento'];

                    // Determinar qué sucursal pasar para obtener el correlativo
                    if (!empty($sucursal_origen) && !empty($sucursal_destino)) {
                        // Si ambos existen, pasar la sucursal origen
                        $sucursalParaCorrelativo = $sucursal_origen;
                    } elseif (!empty($sucursal_origen)) {
                        // Solo origen tiene valor
                        $sucursalParaCorrelativo = $sucursal_origen;
                    } elseif (!empty($sucursal_destino)) {
                        // Solo destino tiene valor
                        $sucursalParaCorrelativo = $sucursal_destino;
                    }

                    $noCorrelativo = null;
                    if ($sucursalParaCorrelativo !== null) {
                        $noCorrelativo = $documentosModel->obtenerCorrelativoAutomatico($sucursalParaCorrelativo, $tipo_Movimiento);
                    }

                    if ($noCorrelativo !== null) {
                        $correlativo = $noCorrelativo;
                    }

                    $commonData = [
                        'id_usuario_creacion' => $idUsuarioCreacion,
                        'monto_total' => $total,
                        'estado' => $estado,
                        'noDocumento' => $noDocumento,
                        'correlativo' => $correlativo,
                        'observaciones' => $observacion,
                        'id_proveedor' => $proveedor,
                        'id_tipo_movimiento' => $tipo_Movimiento
                    ];

                    // Agregar id_sucursal_destino si no es null
                    if ($sucursal_destino !== null) {
                        $commonData['id_sucursal_destino'] = $sucursal_destino;
                    }

                    // Agregar id_sucursal_origen si no es null
                    if ($sucursal_origen !== null) {
                        $commonData['id_sucursal_origen'] = $sucursal_origen;
                    }

                    $id_documento = $documentosModel->insertDocumento($commonData);
                    if (!empty($id_documento)) {
                        // Guardar los productos relacionados con el documento
                        foreach ($productos as $producto) {
                            $dataProducto = [
                                'id_documento' => $id_documento,
                                'id_producto' => $producto['id_producto'],
                                'cantidad' => isset($producto['cantidad']) ? $producto['cantidad'] : $producto['cantTraslado'],
                            ];
                            $documentosModel->insertProductoDocumento($dataProducto);

                            // Determinar la sucursal afectada por el movimiento
                            $id_sucursal_movimiento = null;
                            $tiposMovimientosModel = new TiposMovimientosModel();
                            $tipo_movD = $tiposMovimientosModel->getDescripcionByIdMov($tipo_Movimiento);
                            $tipo_mov = $tipo_movD['tipo_mov'];

                            if (in_array($tipo_mov, [0, 1, 2])) {
                                $id_sucursal_movimiento = $sucursal_destino ?? null;
                            } elseif (in_array($tipo_mov, [3, 6])) {
                                $id_sucursal_movimiento = $sucursal_origen ?? null;
                            }
                            // Insertar movimiento
                            $dataMovimiento = [
                                'id_producto' => $producto['id_producto'],
                                'id_tipo_movimiento' => $tipo_Movimiento,
                                'cantidad' => isset($producto['cantidad']) ? $producto['cantidad'] : $producto['cantTraslado'],
                                'descripcion' => $descripcionMovimiento,
                                'id_solicitud' => null,
                                'id_documento' => $id_documento,
                                'id_sucursal_movimiento' => $id_sucursal_movimiento
                            ];
                            $flagSave = $movimientosModel->insertMovimiento($dataMovimiento);
                        }
                    }
                }
                if ($flagSave) {
                    return $this->response->setJSON(['success' => true, 'message' => 'Movimiento registrado con éxito.']);
                } else {
                    return $this->response->setJSON(['success' => false, 'message' => 'Ocurrio un error durante el proceso']);
                }
            } else {
                return redirect()->to(base_url());
            }
        } catch (\Throwable $e) {
            $errorMessage = 'Ocurrió un error: ' . $e->getMessage() . PHP_EOL;
            $errorMessage .= 'Trace: ' . $e->getTraceAsString();

            log_message('error', $errorMessage);
            return $this->response->setJSON(['error' => "Al parecer hay un error al procesar los ingresar_movimiento."]);
        }
    }

    /* public function ingresar_movimiento()
    {
        try {
            $session = session();
            if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
                log_message("info", $this->nameClass . " ingresar_movimiento");

                $inputJSON = $this->request->getBody();

                $input = json_decode($inputJSON, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception('Error al decodificar JSON: ' . json_last_error_msg());
                }
                $flagSave = false;
                $tipo_Movimiento = $input['tipo_Movimiento'];


                $documentosModel = new DocumentosModel();
                $movimientosModel = new MovimientosModel();
                $descripcionMovimiento = $movimientosModel->getDescripcionById($tipo_Movimiento);

                if ($tipo_Movimiento === '2') {
                    log_message("info", $this->nameClass . " ingresar_movimiento es nota de remision flujo de entrada y salida ");

                    $idUsuarioCreacion = $_SESSION['id_usuario'];
                    $correlativo = $input['correlativo'];
                    $noDocumento = $input['noDocumento'];
                    $sucursal_destino = $input['sucursal_destino'];
                    $observacion = 'Ingreso por nota de remision: ' . $input['observacion'];
                    $estado = $input['estado'];
                    $sucursal_origen = $input['sucursal_origen'];
                    $proveedor = $input['proveedor'];

                    $productos = $input['productos'];
                    $commonData = [
                        'id_usuario_creacion' => $idUsuarioCreacion,
                        'correlativo' => $correlativo,
                        'noDocumento' => $noDocumento,
                        'estado' => $estado,
                        'id_proveedor' => $proveedor
                    ];


                    $dataDocumentoEntrada = array_merge($commonData, [
                        'id_sucursal_destino' => $sucursal_destino,
                        'id_sucursal_origen' => $sucursal_origen,
                        'observaciones' => 'Ingreso: ' . $observacion,
                        'id_tipo_movimiento' => 2
                    ]);


                    $dataDocumentoSalida = array_merge($commonData, [
                        'id_sucursal_destino' => $sucursal_origen,
                        'id_sucursal_origen' => $sucursal_destino,
                        'observaciones' => 'Salida: ' . $observacion,
                        'id_tipo_movimiento' => 6
                    ]);


                    $id_documento_entrada = $documentosModel->insertDocumento($dataDocumentoEntrada);

                    $id_documento_salida = $documentosModel->insertDocumento($dataDocumentoSalida);

                    if (!empty($id_documento_entrada) && !empty($id_documento_salida)) {
                        $flagSaveEntrada = $this->guardarProductos($productos, $id_documento_entrada, 2, $sucursal_destino);

                        $flagSaveSalida = $this->guardarProductos($productos, $id_documento_salida, 6, $sucursal_origen);

                        $flagSave = $flagSaveEntrada && $flagSaveSalida;
                    }
                } else {
                    log_message("info", " ingresar_movimiento no es nota de remision flujo de entrada");
                    $idUsuarioCreacion = $_SESSION['id_usuario'];
                    $correlativo = $input['correlativo'];
                    $noDocumento = $input['noDocumento'];
                    $sucursal_destino = isset($input['sucursal_destino']) ? $input['sucursal_destino'] : null;
                    $sucursal_origen = isset($input['sucursal_origen']) ? $input['sucursal_origen'] : null;
                    $observacion = $input['observacion'];
                    $productos = $input['productos'];
                    $estado = $input['estado'];
                    $total = isset($input['total']) ? $input['total'] : 0.00;
                    $proveedor = isset($input['proveedor']) ? $input['proveedor'] : null;
                    $tipo_Movimiento = $input['tipo_Movimiento'];

                    $commonData = [
                        'id_usuario_creacion' => $idUsuarioCreacion,
                        'monto_total' => $total,
                        'estado' => $estado,
                        'noDocumento' => $noDocumento,
                        'correlativo' => $correlativo,
                        'observaciones' => $observacion,
                        'id_proveedor' => $proveedor,
                        'id_tipo_movimiento' => $tipo_Movimiento
                    ];

                    // Agregar id_sucursal_destino si no es null
                    if ($sucursal_destino !== null) {
                        $commonData['id_sucursal_destino'] = $sucursal_destino;
                    }

                    // Agregar id_sucursal_origen si no es null
                    if ($sucursal_origen !== null) {
                        $commonData['id_sucursal_origen'] = $sucursal_origen;
                    }

                    $id_documento = $documentosModel->insertDocumento($commonData);
                    if (!empty($id_documento)) {
                        // Guardar los productos relacionados con el documento
                        foreach ($productos as $producto) {
                            $dataProducto = [
                                'id_documento' => $id_documento,
                                'id_producto' => $producto['id_producto'],
                                'cantidad' => isset($producto['cantidad']) ? $producto['cantidad'] : $producto['cantTraslado'],
                            ];
                            $documentosModel->insertProductoDocumento($dataProducto);

                            // Determinar la sucursal afectada por el movimiento
                            $id_sucursal_movimiento = null;
                            $tiposMovimientosModel = new TiposMovimientosModel();
                            $tipo_movD = $tiposMovimientosModel->getDescripcionByIdMov($tipo_Movimiento);
                            $tipo_mov = $tipo_movD['tipo_mov'];

                            if (in_array($tipo_mov, [0, 1, 2])) {
                                $id_sucursal_movimiento = $sucursal_destino ?? null;
                            } elseif (in_array($tipo_mov, [3, 6])) {
                                $id_sucursal_movimiento = $sucursal_origen ?? null;
                            }
                            // Insertar movimiento
                            $dataMovimiento = [
                                'id_producto' => $producto['id_producto'],
                                'id_tipo_movimiento' => $tipo_Movimiento,
                                'cantidad' => isset($producto['cantidad']) ? $producto['cantidad'] : $producto['cantTraslado'],
                                'descripcion' => $descripcionMovimiento,
                                'id_solicitud' => null,
                                'id_documento' => $id_documento,
                                'id_sucursal_movimiento' => $id_sucursal_movimiento
                            ];
                            $flagSave = $movimientosModel->insertMovimiento($dataMovimiento);
                        }
                    }
                }
                if ($flagSave) {
                    return $this->response->setJSON(['success' => true, 'message' => 'Movimiento registrado con éxito.']);
                } else {
                    return $this->response->setJSON(['success' => false, 'message' => 'Ocurrio un error durante el proceso']);
                }
            } else {
                return redirect()->to(base_url());
            }
        } catch (\Throwable $e) {
            $errorMessage = 'Ocurrió un error: ' . $e->getMessage() . PHP_EOL;
            $errorMessage .= 'Trace: ' . $e->getTraceAsString();

            log_message('error', $errorMessage);
            return $this->response->setJSON(['error' => "Al parecer hay un error al procesar los ingresar_movimiento."]);
        }
    } */

    public function guardarProductos($productos, $id_documento, $id_tipo_movimiento, $id_sucursal, $descripcionPersonalizada = null)
    {
        try {
            log_message("debug", "Llego al metodo de guardarProductos");
            $documentosModel = new DocumentosModel();
            $movimientosModel = new MovimientosModel();

            foreach ($productos as $producto) {
                log_message("debug", "Cantidad usada: " . ($producto['cantTraslado'] ?? $producto['cantidad']));

                $dataProducto = [
                    'id_documento' => $id_documento,
                    'id_producto' => $producto['id_producto'],
                    //'cantidad' => $producto['cantTraslado']
                    'cantidad' => $producto['cantTraslado'] ?? $producto['cantidad']
                ];
                $resultProducto = $documentosModel->insertProductoDocumento($dataProducto);
                log_message("debug", "Resultado de insertar producto: " . ($resultProducto ? 'Éxito' : 'Fallo'));
                if (!$resultProducto) {
                    return false;
                }

                $descripcion = ($id_tipo_movimiento == 2) ? 'Ingreso: Ingreso por nota de remisión' : 'Salida: Salida por nota de remisión';

                if (!empty($descripcionPersonalizada)) {
                    $descripcion .= ' - ' . $descripcionPersonalizada;
                }

                $dataMovimiento = [
                    'id_producto' => $producto['id_producto'],
                    'id_tipo_movimiento' => $id_tipo_movimiento,
                    //'cantidad' => $producto['cantTraslado'],
                    'cantidad' => $producto['cantTraslado'] ?? $producto['cantidad'],
                    'descripcion' => $descripcion,
                    'id_solicitud' => null,
                    'id_documento' => $id_documento,
                    'id_sucursal_movimiento' => $id_sucursal
                ];
                $resultMovimiento = $movimientosModel->insertMovimiento($dataMovimiento);
                log_message("debug", "Resultado de insertar movimiento: " . ($resultMovimiento ? 'Éxito' : 'Fallo'));
                if (!$resultMovimiento) {
                    return false;
                }
            }

            return true;
        } catch (\Throwable $e) {
            $errorMessage = 'Ocurrió un error: ' . $e->getMessage() . PHP_EOL;
            $errorMessage .= 'Trace: ' . $e->getTraceAsString();

            log_message('error', $errorMessage);
            return false;
        }
    }

    public function responder_remision()
    {
        try {
            $session = session();
            $input = $this->request->getJSON(true);
            $id_documento = $input['id_documento']; // documento de salida
            $respuesta = $input['respuesta']; // 'aceptado' o 'rechazado'

            $flagSave = false;

            $documentosModel = new DocumentosModel();
            $movimientosModel = new MovimientosModel();
            if (!isset($_SESSION['id_usuario'])) {
                return $this->response->setJSON(['success' => false, 'message' => 'Sesión no válida']);
            }


            $documento = $documentosModel->find($id_documento);
            if (!$documento) {
                return $this->response->setJSON(['success' => false, 'message' => 'Documento no encontrado']);
            }

            $productos = $documentosModel->obtenerProductosXDocumentos($id_documento); // necesitas este método
            log_message('debug', 'ID Documento: ' . $id_documento);
            log_message('debug', 'Respuesta: ' . $respuesta);
            log_message('debug', 'Documento: ' . print_r($documento, true));
            log_message('debug', 'Productos: ' . print_r($productos, true));


            if ($respuesta === 'aceptado') {
                // Hacer entrada en sucursal destino
                $dataEntrada = [
                    'id_usuario_creacion' => $_SESSION['id_usuario'],
                    'correlativo' => $documento['correlativo'],
                    'noDocumento' => $documento['noDocumento'],
                    'estado' => 'Aceptado',
                    'id_proveedor' => $documento['id_proveedor'],
                    'id_sucursal_origen' => $documento['id_sucursal_origen'],
                    'id_sucursal_destino' => $documento['id_sucursal_destino'],
                    'observaciones' => 'Entrada por remisión aceptada',
                    'id_tipo_movimiento' => 2
                ];
                $idEntrada = $documentosModel->insertDocumento($dataEntrada);

                /* foreach ($productos as $p) {
                    $movimientosModel->insertMovimiento([
                        'id_documento' => $idEntrada,
                        'id_producto' => $p['id_producto'],
                        'cantidad' => $p['cantidad'],
                        'descripcion' => 'Entrada aceptada',
                        'id_tipo_movimiento' => 2,
                        'id_sucursal_movimiento' => $documento['id_sucursal_destino']
                    ]);
                } */

                if (!empty($idEntrada)) {
                    $flagSave = $this->guardarProductos($productos, $idEntrada, 2, $documento['id_sucursal_destino'], 'Entrada aceptada');
                }
            } elseif ($respuesta === 'rechazado') {
                // Hacer entrada de regreso a sucursal origen
                $dataDevolucion = [
                    'id_usuario_creacion' => $_SESSION['id_usuario'],
                    'correlativo' => $documento['correlativo'],
                    'noDocumento' => $documento['noDocumento'],
                    'estado' => 'Rechazado',
                    'id_proveedor' => $documento['id_proveedor'],
                    'id_sucursal_origen' => $documento['id_sucursal_destino'],
                    'id_sucursal_destino' => $documento['id_sucursal_origen'],
                    'observaciones' => 'Devolución por rechazo de remisión',
                    'id_tipo_movimiento' => 2
                ];
                $idDevolucion = $documentosModel->insertDocumento($dataDevolucion);

                /* foreach ($productos as $p) {
                    $movimientosModel->insertMovimiento([
                        'id_documento' => $idDevolucion,
                        'id_producto' => $p['id_producto'],
                        'cantidad' => $p['cantidad'],
                        'descripcion' => 'Entrada por rechazo',
                        'id_tipo_movimiento' => 2,
                        'id_sucursal_movimiento' => $documento['id_sucursal_origen']
                    ]);
                } */

                if (!empty($idDevolucion)) {
                    $flagSave = $this->guardarProductos($productos, $idDevolucion, 2, $documento['id_sucursal_origen'], 'Entrada por rechazo');
                }
            }

            // Actualizar estado del documento original a aceptado/rechazado
            $documentosModel->update($id_documento, ['estado' => ucfirst($respuesta)]);

            if ($flagSave) {
                return $this->response->setJSON(['success' => true, 'message' => 'Respuesta procesada correctamente.']);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'Error al guardar productos o movimientos.']);
            }
            //return $this->response->setJSON(['success' => true, 'message' => 'Respuesta procesada correctamente.']);
        } catch (\Throwable $e) {
            log_message('error', $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Error al procesar respuesta.']);
        }
    }
}
