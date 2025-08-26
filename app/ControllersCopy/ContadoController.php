<?php

namespace App\Controllers;


use App\Models\SolicitudModel;
use App\Models\ProductoSolicitudModel;
use App\Models\MovimientosModel;
use App\Models\CobrosModel;

use function PHPUnit\Framework\isEmpty;

class ContadoController extends BaseController
{
    private $solicitudesModel;
    private $prodSolicitudModel;
    private $movimientosModel;
    private $cobrosModel;

    public function __construct()
    {
        $this->solicitudesModel = new SolicitudModel();
        $this->prodSolicitudModel = new ProductoSolicitudModel();
        $this->movimientosModel = new MovimientosModel();
        $this->cobrosModel = new CobrosModel();
    }
    public function index()
    {
        $session = session();
        if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
            $content4 = view('solicitudes/form_contado');
            $fullPage = $this->renderPage($content4);
            return $fullPage;
        } else {
            return redirect()->to(base_url());
        }
    }

    public function procesarSolicitudContado()
    {
        $db = \Config\Database::connect(); // Obtiene la conexión a la base de datos
        $db->transBegin(); // Inicia la transacción

        try {
            $session = session();
            $data = $this->request->getJSON(true);
            log_message('info', 'Datos recibidos: ' . print_r($data, true));
            if (!empty($data['id_cliente'])) {

                // ==========================================
                // PASO 1: Crear la solicitud
                // ==========================================
                log_message('info', '************ PASO 1: CREAR SOLICITUD ************');

                $montoApagar = (float)$data['saldoAPagar'];

                $dataSoli = [
                    'id_cliente'          => $data['id_cliente'],
                    'id_usuario_creacion' => $_SESSION['id_usuario'],
                    'id_estado_actual'    => 2,
                    'id_sucursal'         => $_SESSION['sucursal'],
                    'monto_solicitud'     => $montoApagar,
                    'montoApagar'         => $montoApagar,
                    'tipo_solicitud'      => 'CONTADO'
                ];

                $this->solicitudesModel->insert($dataSoli);
                $id_solicitud_creada = $this->solicitudesModel->insertID();
                log_message('info', 'Solicitud creada - ID: ' . $id_solicitud_creada);

                // Obtener el número de solicitud
                $solicitud = $this->solicitudesModel->find($id_solicitud_creada);
                $numero_solicitud = $solicitud['numero_solicitud'];
                log_message('info', 'Número de solicitud creada: ' . $numero_solicitud);

                // ==========================================
                // PASO 2: Crear los productos de la solicitud
                // ==========================================
                log_message('info', '************ PASO 2: CREAR PRODUCTOS ************');

                if (!empty($this->crearProductosSolicitud($data['productos'], $id_solicitud_creada))) {
                    throw new \Exception('Error al crear los productos por solicitud.');
                }

                log_message('info', '********** FIN PASO 2: PRODUCTOS CREADOS **********');

                // ==========================================
                // PASO 3: Registrar el movimiento de salida
                // ==========================================
                log_message('info', '***** PASO 3: REGISTRAR MOVIMIENTO DE SALIDA *****');

                if (!$this->registrarMovimientoSalidaVenta($data['productos'], $id_solicitud_creada, $numero_solicitud)) {
                    throw new \Exception('Error al generar los movimientos.');
                }

                log_message('info', '****** FIN PASO 3: MOVIMIENTOS REGISTRADOS ******');

                // ==========================================
                // PASO 4: Registrar cobro
                // ==========================================
                log_message('info', '***** PASO 4: REGISTRAR COBROS *****');
                if (!$this->crearCobroContado($id_solicitud_creada)) {
                    throw new \Exception('Error al generar los COBROS.');
                }
                log_message('info', '****** FIN PASO 4: COBROS REGISTRADOS ******');
                // ==========================================
                // Finalizar la transacción
                // ==========================================
                $db->transCommit();

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'La solicitud se generó exitosamente, con el numero de solicitud ' . $numero_solicitud
                ]);
            } else {
                log_message("info", "Entro en el Else");
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'El proceso actual solo funciona para clientes registrados.'
                ]);
            }
        } catch (\Throwable $th) {
            $db->transRollback(); // Revierte la transacción en caso de error
            log_message('error', 'Error en procesarSolicitudContado: ' . $th->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Ocurrió un error al procesar la solicitud.'
            ]);
        }
    }


    public function registrarMovimientoSalidaVenta($datos, $id_solicitud, $numSol): bool
    {
        try {
            $session = session();

            if ($session->get('sesion_activa') && $session->get('sesion_activa') === true) {
                // Iterar sobre los productos relacionados con la solicitud
                foreach ($datos as $producto) {
                    // Insertar movimiento
                    $dataMovimiento = [
                        'id_producto' => $producto['id_producto'],
                        'id_tipo_movimiento' => 11, // Salida por venta
                        'cantidad' => $producto['cantidad'],
                        'descripcion' => 'Salida por venta, No-solicitud ' . $numSol,
                        'id_solicitud' => $id_solicitud,
                        'id_documento' => null,
                        'id_sucursal_movimiento'=>$_SESSION['sucursal'],
                        'p_contado' => $producto['precio'],
                    ];
                    $this->movimientosModel->insertMovimiento($dataMovimiento);
                }
                return true;
            } else {
                redirect()->to(base_url());
                return false;
            }
        } catch (\Throwable $e) {
            $errorMessage = 'Ocurrió un error: ' . $e->getMessage() . PHP_EOL;
            $errorMessage .= 'Trace: ' . $e->getTraceAsString();

            log_message('error', $errorMessage);
            return false;
        }
    }

    private function crearProductosSolicitud(array $productosSolicitud, int $idSolicitud): array
    {
        $errores = [];
        if (!empty($productosSolicitud)) {
            foreach ($productosSolicitud as $index => $producto) {
                try {
                    $data = [
                        'id_solicitud'       => $idSolicitud,
                        'id_producto'        => $producto['id_producto'],
                        'cantidad_producto'  => $producto['cantidad']
                    ];

                    $this->prodSolicitudModel->insert($data);
                } catch (\Throwable $th) {
                    $errores[] = [
                        'index' => $index,
                        'error' => $th->getMessage(),
                        'producto' => $producto
                    ];
                }
            }
        }

        return $errores;
    }

    public function crearCobroContado($id_solicitud)
    {
        try {
            $solicitudEncontrada = $this->solicitudesModel->find($id_solicitud);
            $fechaCompleta = $solicitudEncontrada["fecha_creacion"];
            $fechaSol = explode(" ", $fechaCompleta)[0];
            $fecha = new \DateTime($fechaSol);

            // Generar un solo cobro para pago de contado
            $data = [
                'id_solicitud'      => $id_solicitud,
                'numero_cuota'      => 1,  // Se registra como una única cuota
                'monto_cuota'       => $solicitudEncontrada['monto_solicitud'], // Monto total del pago
                'descripcion'       => "Pago de contado",
                'estado'            => "PENDIENTE",
                'fecha_pago'        => $fecha->format("Y-m-d"),
                'fecha_vencimiento' => $fecha->format("Y-m-d"), // Fecha de creación como vencimiento
                'esPrima'           => 0
            ];

            if ($this->cobrosModel->insert($data)) {
                return true;
            } else {
                log_message('info', "Error al guardar cobro de contado");
                return false;
            }
        } catch (\Throwable $e) {
            $errorMessage = 'Ocurrió un error: ' . $e->getMessage() . PHP_EOL;
            $errorMessage .= 'Trace: ' . $e->getTraceAsString();
            log_message('error', $errorMessage);
            return $this->response->setJSON(['error' => 'Error al procesar la generación de cobros']);
        }
    }
}
