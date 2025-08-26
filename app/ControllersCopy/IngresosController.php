<?php

namespace App\Controllers;

use App\Controllers\ProveedorController;
use App\Models\DocumentosModel;
use App\Models\MovimientosModel;
use App\Models\SucursalesModel;
use App\Models\TiposMovimientosModel;
use TCPDF;
use Dompdf\Dompdf;

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

                $proveedoresActivos = $proveedoresController->getProveedoresAllActives();
                $sucursales = $sucursalesModel->getSucursalesAll();
                $tiposMovimientos = $tiposMovimientosModel->findAll();

                $data = [
                    'proveedoresActivos' => $proveedoresActivos,
                    'sucursales' => $sucursales,
                    'tiposMovimientos' => $tiposMovimientos,
                    'tiposMovimientosM' => $tiposMovimientos
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
    }

    public function guardarProductos($productos, $id_documento, $id_tipo_movimiento, $id_sucursal)
    {
        try {
            log_message("debug", "Llego al metodo de guardarProductos");
            $documentosModel = new DocumentosModel();
            $movimientosModel = new MovimientosModel();

            foreach ($productos as $producto) {
                $dataProducto = [
                    'id_documento' => $id_documento,
                    'id_producto' => $producto['id_producto'],
                    'cantidad' => $producto['cantTraslado']
                ];
                $resultProducto = $documentosModel->insertProductoDocumento($dataProducto);
                log_message("debug", "Resultado de insertar producto: " . ($resultProducto ? 'Éxito' : 'Fallo'));
                if (!$resultProducto) {
                    return false;
                }

                $descripcion = ($id_tipo_movimiento == 2) ? 'Ingreso: Ingreso por nota de remisión' : 'Salida: Salida por nota de remisión';

                $dataMovimiento = [
                    'id_producto' => $producto['id_producto'],
                    'id_tipo_movimiento' => $id_tipo_movimiento,
                    'cantidad' => $producto['cantTraslado'],
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
}
