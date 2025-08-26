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

use function PHPUnit\Framework\isEmpty;

class IngresoPorComprasController extends BaseController
{
    private $nameClass = "IngresoPorComprasController";

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
                    'fechaVirtual' => $fechaVirtual
                ];


                $content4 = view('movimientos/ingreso_x_compra', $data);
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

    public function registrarMovimientoIngreso()
    {
        try {

            log_message("info", $this->nameClass . " registrarMovimientoIngreso");
            $session = session();
            if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
                // Obtener el cuerpo de la solicitud
                $inputJSON = $this->request->getBody();
                // Decodificar los datos JSON
                $input = json_decode($inputJSON, true);
                // Verificar si la decodificación fue exitosa
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception('Error al decodificar JSON: ' . json_last_error_msg());
                }

                // Extraer datos del JSON
                $idUsuarioCreacion = $_SESSION['id_usuario']; // Asumiendo que el ID del usuario está almacenado en la sesión
                /* $fecha = $input['fecha']; */
                /* $estado = $input['estado']; */
                $estado = ($input['estado'] === 'Procesado') ? 'Aceptado' : $input['estado'];
                $correlativo = $input['correlativo'];
                $noDocumento = $input['noDocumento'];
                $sucursal = $input['id_sucursal_destino'];
                $proveedor = $input['proveedor'];
                $observacion = $input['observacion'];
                $total = $input['total'];
                $productos = $input['productos'];
                $tipo_Movimiento = 1;

                // Guardar datos en la base de datos
                $documentosModel = new DocumentosModel();
                $movimientosModel = new MovimientosModel();
                $noCorrelativo = $documentosModel->obtenerCorrelativoAutomatico($sucursal,$tipo_Movimiento);

                if ($noCorrelativo !== null) {
                    $correlativo = $noCorrelativo;
                }

                $dataDocumento = [
                    'id_usuario_creacion' => $idUsuarioCreacion,
                    'id_sucursal_destino' => $sucursal,
                    'id_proveedor' => $proveedor,
                    'monto_total' => $total,
                    'estado' => $estado,
                    'noDocumento' => $noDocumento,
                    'correlativo' => $correlativo,
                    'observaciones' => $observacion,
                    'id_tipo_movimiento' => $tipo_Movimiento
                ];

                // Insertar documento y obtener el ID del documento insertado
                $id_documento = $documentosModel->insertDocumento($dataDocumento);

                // Guardar los productos relacionados con el documento
                foreach ($productos as $producto) {
                    $dataProducto = [
                        'id_documento' => $id_documento,
                        'id_producto' => $producto['id_producto'],
                        'cantidad' => $producto['cantidad']
                    ];
                    $documentosModel->insertProductoDocumento($dataProducto);

                    // Insertar movimiento
                    $dataMovimiento = [
                        'id_producto' => $producto['id_producto'],
                        'id_tipo_movimiento' => 1, // Asumiendo que 1 es el tipo de movimiento para ingreso
                        'cantidad' => $producto['cantidad'],
                        'descripcion' => 'Ingreso por compra',
                        'id_solicitud' => null, // Asumiendo que no hay solicitud en este contexto
                        'id_documento' => $id_documento,
                        'id_sucursal_movimiento' => $sucursal
                    ];
                    $movimientosModel->insertMovimiento($dataMovimiento);
                }

                // Enviar respuesta exitosa
                return $this->response->setJSON(['success' => true, 'message' => 'Movimiento de ingreso registrado con éxito.']);
            } else {
                return redirect()->to(base_url());
            }
        } catch (\Throwable $e) {
            $errorMessage = 'Ocurrió un error: ' . $e->getMessage() . PHP_EOL;
            $errorMessage .= 'Trace: ' . $e->getTraceAsString();

            log_message('error', $errorMessage);
            return $this->response->setJSON(['error' => "Al parecer hay un error al procesar los datos."]);
        }
    }

    public function obtenerDocumentos($idTipoDoc = null)
    {
        try {
            $session = session();
            log_message('debug', 'el valor del idTipoDoc es--->  '.$idTipoDoc);
            $documentosModel = new DocumentosModel();

            if ($idTipoDoc !== null) {
                if ($idTipoDoc ==0) {
                    log_message('debug', 'entro porque a obtener notas de remision procesadas');
                    $documentos = $documentosModel->obtenerProcesadosSucursal();
                }else {
                    log_message('debug', 'entro porque idTipoDoc es diferente de null');
                    $documentos = $documentosModel->obtenerDocumentosPorId($idTipoDoc);
                }
            } else {
                log_message('debug', 'entro al else porque idTipoDoc es null');
                $documentos = $documentosModel->obtenerDocumentos();
            }

            $response = [
                'success' => true,
                'documentos' => $documentos,
                'sucActual' => $_SESSION['sucursal']
            ];

            return $this->response->setJSON($response);
        } catch (\Throwable $e) {
            $errorMessage = 'Ocurrió un error: ' . $e->getMessage() . PHP_EOL;
            $errorMessage .= 'Trace: ' . $e->getTraceAsString();

            log_message('error', $errorMessage);

            return $this->response->setJSON(['error' => 'Error al buscar documentos.']);
        }
    }

    public function generarPdf()
    {
        try {
            $session = session();
            if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
                $dompdf = new \Dompdf\Dompdf();
    
                $idDocumento = $this->request->getVar('id_documento');
    
                $documentosModel = new DocumentosModel();
                $tiposMovimientosModel = new TiposMovimientosModel();
                $documento = $documentosModel->obtenerDocumentoPorId($idDocumento);
    
                log_message('debug', "Información del documento: " . print_r($documento, true));
    
                $idDocumentoExtraido = $documento['id_documento'] ?? null;
                $tipo_mov = $documento['tipo_mov'] ?? null;

                if ($idDocumentoExtraido != null && !empty($idDocumentoExtraido)) {
                    $productos = $documentosModel->obtenerProductosXDocumentos($idDocumentoExtraido);
                } else {
                    return $this->response->setJSON(['error' => 'ID del documento no válido.']);
                }

                //para notas de remision
                if ($documento['id_tipo_movimiento'] == 2 || $documento['id_tipo_movimiento'] == 6) {

                    $documento = ($documento['id_tipo_movimiento'] == 6) ? $documentosModel->obtenerDocumentoPorIdNotaRemiSalida($idDocumento) : $documentosModel->obtenerDocumentoPorIdNotaRemiEntrada($idDocumento);
                    log_message('debug', "Información del documento de NR: " . print_r($documento, true));

                    $titulo = ($documento['id_tipo_movimiento'] == 2) ? 'Ingreso por nota de remisión' : 'Salida por nota de remisión';

                    $html = view('PDF_ALL/nota_remision', ['titulo' => $titulo,'documento' => $documento, 'productos' => $productos]);
                }elseif($documento['id_tipo_movimiento'] == 1) {// para ingresos por compras
                    $html = view('PDF_ALL/ing_x_compras', ['documento' => $documento, 'productos' => $productos]);
                }else {
                    $documento = $documentosModel->obtenerDocumentoPorId($idDocumento);
                    log_message('debug', "Información del documento " . print_r($documento, true));

                    $tipoMovimiento = $tiposMovimientosModel->getDescripcionById($idDocumento);
                
                    if ($tipoMovimiento) {
                        $titulo = $tipoMovimiento['descripcion'];
                    } else {
                        $titulo = 'Tipo de movimiento no encontrado';
                    }
                
                    $html = view('PDF_ALL/salidas', [
                        'titulo' => $titulo,
                        'documento' => $documento,
                        'productos' => $productos
                    ]);
                }
                
                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();

                $pathDir = FCPATH . 'public/documentos/movimientos/pdf/';
                if (!is_dir($pathDir)) {
                    mkdir($pathDir, 0777, true);
                }
    
                /* $pdfFilePath = WRITEPATH . 'pdf/' . $documento['noDocumento'] . '.pdf'; */
                $pdfFilePath = FCPATH . 'public/documentos/movimientos/pdf/' . $documento['noDocumento'] .'-'.$tipo_mov . '.pdf';

                file_put_contents($pdfFilePath, $dompdf->output());
    
                $filename = 'documento_' . $documento['noDocumento'] . '.pdf';
                return $this->response->setJSON([
                    'success' => true,
                    'url' => base_url('public/documentos/movimientos/pdf/' . $documento['noDocumento'] .'-'.$tipo_mov . '.pdf')
                ]);
            } else {
                return redirect()->to(base_url());
            }
        } catch (\Throwable $e) {
            $errorMessage = 'Ocurrió un error: ' . $e->getMessage() . PHP_EOL;
            $errorMessage .= 'Trace: ' . $e->getTraceAsString();
            log_message('error', $errorMessage);
            return $this->response->setJSON(['error' => 'Error al generar el documento.']);
        }
    }
    
}
