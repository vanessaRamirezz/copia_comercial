<?php

namespace App\Controllers;

use App\Models\SolicitudModel;
use App\Models\ReferenciaLaboralModel;
use App\Models\ReferenciaFamiliarModel;
use App\Models\ReferenciasNoFamiliaresModel;
use App\Models\EstudioSocioEconomicoModel;
use App\Models\ProductoSolicitudModel;
use App\Models\PlanDePagoModel;
use App\Models\CodeudorModel;
use App\Models\ReferenciaCodeudorModel;
use App\Models\MovimientosModel;
use App\Models\ReferenciasCrediticiasModel;
use App\Models\ClientesModel;
use App\Models\MunicipiosModel;
use App\Models\DepartamentosModel;
use App\Models\ColoniasModel;
use App\Models\CobrosModel;
use DateTime;

use App\Controllers\GenerarSolicitudCreditoController;
use App\Models\DistritosModel;

class SolicitudesController extends BaseController
{
    private $nameClass = "SolicitudesController";

    private $solicitudesModel;
    private $refLaboralModel;
    private $refFamiliresModel;
    private $refNoFamiliresModel;
    private $estudioSocioEconomicoModel;
    private $prodSolicitudModel;
    private $planDePagoModel;
    private $codeudorModel;
    private $refcodeudorModel;
    private $movimientosModel;
    private $refCrediticias;
    private $clientesModel;
    private $deptoModel;
    private $muniModel;
    private $distritoModel;
    private $coloniaModel;
    private $generarDocController;
    private $cobrosModel;

    public function __construct()
    {
        $this->solicitudesModel = new SolicitudModel();
        $this->refLaboralModel = new ReferenciaLaboralModel();
        $this->refFamiliresModel = new ReferenciaFamiliarModel();
        $this->refNoFamiliresModel = new ReferenciasNoFamiliaresModel();
        $this->estudioSocioEconomicoModel = new EstudioSocioEconomicoModel();
        $this->prodSolicitudModel = new ProductoSolicitudModel();
        $this->planDePagoModel = new PlanDePagoModel();
        $this->codeudorModel = new CodeudorModel();
        $this->refcodeudorModel = new ReferenciaCodeudorModel();
        $this->movimientosModel = new MovimientosModel();
        $this->refCrediticias = new ReferenciasCrediticiasModel();
        $this->clientesModel = new ClientesModel();
        $this->deptoModel = new DepartamentosModel();
        $this->distritoModel = new DistritosModel();
        $this->muniModel = new MunicipiosModel();
        $this->coloniaModel = new ColoniasModel();
        $this->generarDocController = new GenerarSolicitudCreditoController();
        $this->cobrosModel = new CobrosModel();
    }

    public function index()
    {
        $session = session();
        if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
            $url = $this->request->uri->getSegment(1);
            log_message("info", $this->nameClass . " " . $url);
            $accesos = json_decode($_SESSION['accesos'], true);
            $allowedUrls = array_column($accesos, 'url_acceso');
            if (in_array($url, $allowedUrls)) {
                $solicitudesCreadas = $this->solicitudesModel->solicitudPorSucursalEstadoCreadas($_SESSION['sucursal']);
                $solicitudesEstVarios = $this->solicitudesModel->solicitudPorSucursalEstadoVarias($_SESSION['sucursal']);
                $data = [
                    'perfil' => $_SESSION['perfilN'],
                    'solicitudesCreadas' => $solicitudesCreadas,
                    'solicitudesVarias' => $solicitudesEstVarios
                ];
                $content4 = view('solicitudes/solicitudes', $data);
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

    public function nuevaSolicitud()
    {
        $session = session();
        if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
            $url = $this->request->uri->getSegment(1);
            log_message("info", $this->nameClass . " " . $url);
            $accesos = json_decode($_SESSION['accesos'], true);
            $allowedUrls = array_column($accesos, 'url_acceso');
            if (in_array($url, $allowedUrls)) {
                $content4 = view('solicitudes/nueva_sol');
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

    /* public function procesarSolicitud()
    {
        try {
            $session = session();
            $data = $this->request->getJSON(true);
            log_message('info', 'Datos recibidos: ' . print_r($data, true));
            # Paso 1: Crear la solicitud
            log_message('info', '*********************************** PASO 1 ***********************************');
            $saldoRestante = (float)$data['plan_de_pago']['montoTotalPagar'] - (float)$data['plan_de_pago']['valorPagoPrima'];
            $dataSoli = [
                'id_cliente' => $data['datos_personales']['id_cliente'],
                'id_usuario_creacion' => $_SESSION['id_usuario'],
                'id_estado_actual' => 1,
                'id_sucursal' => $_SESSION['sucursal'],
                'monto_solicitud' => $data['plan_de_pago']['montoTotalPagar'],
                'montoApagar' => $saldoRestante
            ];

            $this->solicitudesModel->insert($dataSoli);
            $id_solicitud_creada = $this->solicitudesModel->insertID();
            log_message('info', 'solicitud creada id_solicitud: ' . $id_solicitud_creada);

            $solicitud = $this->solicitudesModel->find($id_solicitud_creada);
            $numero_solicitud = $solicitud['numero_solicitud'];
            log_message('info', 'numero solicitud creada: ' . $numero_solicitud);

            if (!$id_solicitud_creada) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al crear la solicitud. Inténtalo de nuevo.'
                ]);
            }
            log_message('info', '********************************* FIN PASO 1 *********************************');

            # Paso 2: Crear las referencias laborales del cliente
            log_message('info', '*********************************** PASO 2 ***********************************');
            $resultadoReferenciaLaboral = $this->crearReferenciasLaborales($data['referencias_laborales'], $id_solicitud_creada);
            if (!$resultadoReferenciaLaboral) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al crear las referencias laborales. Inténtalo de nuevo.'
                ]);
            }
            log_message('info', '********************************* FIN PASO 2 *********************************');

            # Paso 3: Aquí se crean las referencias familiares
            log_message('info', '*********************************** PASO 3 ***********************************');
            $resultadoReferenciasFamiliares = $this->crearReferenciasFamiliares($data['referencias_familiares'], $id_solicitud_creada);
            if (!empty($resultadoReferenciasFamiliares)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al crear las referencias familiares. Inténtalo de nuevo.'
                ]);
            }
            log_message('info', '********************************* FIN PASO 3 *********************************');

            # paso 4: se crean las referencias no familiares del cliente amarrado con el id_solicitud
            log_message('info', '*********************************** PASO 4 ***********************************');
            $resultadoReferenciasNoFamiliares = $this->crearReferenciasNoFamiliares($data['referencias_personas_no_familiar'], $id_solicitud_creada);
            if (!empty($resultadoReferenciasNoFamiliares)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al crear las referencias no familiares. Inténtalo de nuevo.'
                ]);
            }
            log_message('info', '********************************* FIN PASO 4 *********************************');

            # paso 5: se crean las referencias crediticias amarrado con el id_solicitud (si hay referencias)
            log_message('info', '*********************************** PASO 5 ***********************************');
            $resultadoReferenciasCrediticias = $this->crearReferenciasCrediticias($data['referencias_crediticias'], $id_solicitud_creada);
            if (!empty($resultadoReferenciasCrediticias)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al crear las referencias crediticias.'
                ]);
            }
            log_message('info', '********************************* FIN PASO 5 *********************************');

            # paso 6: se crea el analisis socieconomido amarrado al clientes 
            log_message('info', '*********************************** PASO 6 ***********************************');
            $resultEstadoSocioEconomico = $this->crearEstudioSocioEconomico($data['analisis_socioeconomico'], $id_solicitud_creada, $data['datos_personales']['id_cliente']);
            if (!$resultEstadoSocioEconomico) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al crear el estudio socioeconomico.'
                ]);
            }
            log_message('info', '********************************* FIN PASO 6 *********************************');

            # paso 7: El articulo a contratar y el plan de pago ira amarrado a la sol
            log_message('info', '*********************************** PASO 7 ***********************************');
            $resulCrearProductos = $this->crearProductosSolicitud($data['productosSolicitud'], $id_solicitud_creada);
            if (!empty($resulCrearProductos)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al crear los productos por sol.'
                ]);
            }
            log_message('info', '********************************* FIN PASO 7 *********************************');

            # paso 8: El plan de pago ira amarrado a la sol
            log_message('info', '*********************************** PASO 8 ***********************************');
            $resultPlagPago = $this->crearPlanPago($data['plan_de_pago'], $id_solicitud_creada);
            if (!$resultPlagPago) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al crear el plan de pago'
                ]);
            }
            log_message('info', '********************************* FIN PASO 8 *********************************');

            # paso 9: el codeudor se amarra con la solicitud
            log_message('info', '*********************************** PASO 9 ***********************************');
            $restCodeudor = $this->crearCodeudor($data['co_deudor'], $id_solicitud_creada);
            if (!$restCodeudor) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al crear el codeudor'
                ]);
            }
            log_message('info', '********************************* FIN PASO 9 *********************************');

            # paso 10: se genera el movimiento amarrado a la sol
            log_message('info', '*********************************** PASO 10 ***********************************');
            $resMovi = $this->registrarMovimientoSalidaVenta($data['productosSolicitud'], $id_solicitud_creada, $numero_solicitud);
            if (!$resMovi) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al generar los movimientos'
                ]);
            }
            log_message('info', '********************************* FIN PASO 10 *********************************');

            return $this->response->setJSON([
                'success' => true,
                'message' => 'La solicitud se genero exitosamente'
            ]);
        } catch (\Throwable $e) {
            $errorMessage = 'Ocurrió un error: ' . $e->getMessage() . PHP_EOL;
            $errorMessage .= 'Trace: ' . $e->getTraceAsString();
            log_message('error', $errorMessage);
            return $this->response->setJSON(['error' => 'Error al procesar la solicitud.']);
        }
    } */

    public function procesarSolicitud()
    {
        $db = \Config\Database::connect(); // Obtén la conexión a la base de datos
        $db->transBegin(); // Inicia la transacción

        try {
            $session = session();
            $data = $this->request->getJSON(true);
            log_message('info', 'Datos recibidos: ' . print_r($data, true));

            # Paso 1: Crear la solicitud
            log_message('info', '*********************************** PASO 1 ***********************************');
            $saldoRestante = (float)$data['plan_de_pago']['montoTotalPagar'] - (float)$data['plan_de_pago']['valorPagoPrima'];
            $dataSoli = [
                'id_cliente' => $data['datos_personales']['id_cliente'],
                'id_usuario_creacion' => $_SESSION['id_usuario'],
                'id_estado_actual' => 1,
                'id_sucursal' => $_SESSION['sucursal'],
                'monto_solicitud' => $data['plan_de_pago']['montoTotalPagar'],
                'montoApagar' => $saldoRestante
            ];

            $this->solicitudesModel->insert($dataSoli);
            $id_solicitud_creada = $this->solicitudesModel->insertID();
            log_message('info', 'solicitud creada id_solicitud: ' . $id_solicitud_creada);

            $solicitud = $this->solicitudesModel->find($id_solicitud_creada);
            $numero_solicitud = $solicitud['numero_solicitud'];
            log_message('info', 'numero solicitud creada: ' . $numero_solicitud);

            if (!$id_solicitud_creada) {
                throw new \Exception('Error al crear la solicitud.');
            }
            log_message('info', '********************************* FIN PASO 1 *********************************');

            # Paso 2: Crear las referencias laborales del cliente
            log_message('info', '*********************************** PASO 2 ***********************************');
            if (!$this->crearReferenciasLaborales($data['referencias_laborales'], $id_solicitud_creada)) {
                throw new \Exception('Error al crear las referencias laborales.');
            }
            log_message('info', '********************************* FIN PASO 2 *********************************');

            # Paso 3: Crear las referencias familiares
            log_message('info', '*********************************** PASO 3 ***********************************');
            if (!empty($this->crearReferenciasFamiliares($data['referencias_familiares'], $id_solicitud_creada))) {
                throw new \Exception('Error al crear las referencias familiares.');
            }
            log_message('info', '********************************* FIN PASO 3 *********************************');

            # Paso 4: Crear las referencias no familiares
            log_message('info', '*********************************** PASO 4 ***********************************');
            if (!empty($this->crearReferenciasNoFamiliares($data['referencias_personas_no_familiar'], $id_solicitud_creada))) {
                throw new \Exception('Error al crear las referencias no familiares.');
            }
            log_message('info', '********************************* FIN PASO 4 *********************************');

            # Paso 5: Crear las referencias crediticias
            log_message('info', '*********************************** PASO 5 ***********************************');
            if (!empty($this->crearReferenciasCrediticias($data['referencias_crediticias'], $id_solicitud_creada))) {
                throw new \Exception('Error al crear las referencias crediticias.');
            }
            log_message('info', '********************************* FIN PASO 5 *********************************');

            # Paso 6: Crear el análisis socioeconómico
            log_message('info', '*********************************** PASO 6 ***********************************');
            if (!$this->crearEstudioSocioEconomico($data['analisis_socioeconomico'], $id_solicitud_creada, $data['datos_personales']['id_cliente'])) {
                throw new \Exception('Error al crear el estudio socioeconómico.');
            }
            log_message('info', '********************************* FIN PASO 6 *********************************');

            # Paso 7: Crear los productos de la solicitud
            log_message('info', '*********************************** PASO 7 ***********************************');
            if (!empty($this->crearProductosSolicitud($data['productosSolicitud'], $id_solicitud_creada))) {
                throw new \Exception('Error al crear los productos por solicitud.');
            }
            log_message('info', '********************************* FIN PASO 7 *********************************');

            # Paso 8: Crear el plan de pago
            log_message('info', '*********************************** PASO 8 ***********************************');
            if (!$this->crearPlanPago($data['plan_de_pago'], $id_solicitud_creada)) {
                throw new \Exception('Error al crear el plan de pago.');
            }
            log_message('info', '********************************* FIN PASO 8 *********************************');

            # Paso 9: Crear el codeudor
            log_message('info', '*********************************** PASO 9 ***********************************');
            if (!$this->crearCodeudor($data['co_deudor'], $id_solicitud_creada)) {
                throw new \Exception('Error al crear el codeudor.');
            }
            log_message('info', '********************************* FIN PASO 9 *********************************');

            # Paso 10: Registrar el movimiento de salida
            log_message('info', '*********************************** PASO 10 ***********************************');
            if (!$this->registrarMovimientoSalidaVenta($data['productosSolicitud'], $id_solicitud_creada, $numero_solicitud)) {
                throw new \Exception('Error al generar los movimientos.');
            }
            log_message('info', '********************************* FIN PASO 10 *********************************');

            # Commit de la transacción
            $db->transCommit();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'La solicitud se generó exitosamente.'
            ]);
        } catch (\Throwable $e) {
            $db->transRollback(); // Revertir cambios si ocurre un error

            $errorMessage = 'Ocurrió un error: ' . $e->getMessage() . PHP_EOL;
            $errorMessage .= 'Trace: ' . $e->getTraceAsString();
            log_message('error', $errorMessage);

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al procesar la solicitud.'
            ]);
        }
    }


    private function crearReferenciasLaborales(array $referenciasLaborales, int $idSolicitud): bool
    {
        try {
            log_message('info', "el valor de los datos de referencia es:: " . print_r($referenciasLaborales, true));
            $dataRefeLaboral = [
                'id_profesion'             => $referenciasLaborales['profesion_oficio'],
                'empresa'                  => $referenciasLaborales['empresa'],
                'direccion_trabajo'        => $referenciasLaborales['direccion_trabajo'],
                'telefono_trabajo'         => $referenciasLaborales['telefono_trabajo'],
                'cargo'                    => $referenciasLaborales['cargo'],
                'salario'                  => $referenciasLaborales['salario'],
                'tiempo_laborado_empresa'  => $referenciasLaborales['tiempo_laborando_empresa'],
                'nombre_jefe_inmediato'    => $referenciasLaborales['nombre_jefe_inmediato'],
                'empresa_anterior'         => $referenciasLaborales['empresa_anterior'],
                'telefono_empresa_anterior' => $referenciasLaborales['telefono_empresa_anterior'],
                'id_solicitud'             => $idSolicitud
            ];

            $this->refLaboralModel->insert($dataRefeLaboral);
            return $this->refLaboralModel->insertID() ? true : false;
        } catch (\Throwable $e) {
            $errorMessage = 'Ocurrió un error: ' . $e->getMessage() . PHP_EOL;
            $errorMessage .= 'Trace: ' . $e->getTraceAsString();
            log_message('error', $errorMessage);
            return false;
        }
    }

    private function crearReferenciasFamiliares(array $referenciasFamiliares, int $idSolicitud): array
    {
        $errores = [];

        foreach ($referenciasFamiliares as $index => $referencia) {
            try {
                $dataRefeFamiliar = [
                    'id_solicitud'        => $idSolicitud,
                    'nombre'              => $referencia['nombre'],
                    'parentesco'          => $referencia['parentesco'],
                    'direccion'           => $referencia['direccion'],
                    'telefono'            => $referencia['telefono'],
                    'lugar_trabajo'       => $referencia['lugar_trabajo'],
                    'telefono_trabajo'    => $referencia['telefono_trabajo']
                ];

                $this->refFamiliresModel->insert($dataRefeFamiliar);
            } catch (\Throwable $th) {
                # Registrar el error con información relevante
                $errores[] = [
                    'index' => $index,
                    'error' => $th->getMessage(),
                    'referencia' => $referencia
                ];
                log_message('error', 'Error al crear referencia familiar en índice ' . $index . ': ' . $th->getMessage());
            }
        }

        return $errores;
    }



    private function crearReferenciasNoFamiliares(array $referenciasNoFamiliares, int $idSolicitud): array
    {
        $errores = [];

        foreach ($referenciasNoFamiliares as $index => $referencia) {
            try {
                $data = [
                    'id_solicitud'       => $idSolicitud,
                    'nombre'             => $referencia['nombre'],
                    'direccion'          => $referencia['direccion'],
                    'telefono'           => $referencia['telefono'],
                    'lugar_trabajo'      => $referencia['lugar_trabajo'],
                    'telefono_trabajo'   => $referencia['telefono_trabajo']
                ];
                $this->refNoFamiliresModel->insert($data);
            } catch (\Throwable $th) {
                $errores[] = [
                    'index' => $index,
                    'error' => $th->getMessage(),
                    'referencia' => $referencia
                ];
            }
        }
        return $errores;
    }

    private function crearReferenciasCrediticias(array $referenciasCrediticias, int $idSolicitud): array
    {
        $errores = [];
        log_message('info', 'Resultado ref crediticias: ' . print_r($referenciasCrediticias, true));
        if (!empty($referenciasCrediticias)) {
            foreach ($referenciasCrediticias as $index => $referencia) {
                try {
                    $data = [
                        'id_solicitud'   => $idSolicitud,
                        'institucion'    => $referencia['nombre'],  // Ajustado a 'institucion' según el modelo
                        'telefono'       => $referencia['telefono'],
                        'monto_credito'  => $referencia['monto_credito'],
                        'periodos'       => $referencia['periodos'],
                        'plazo'          => $referencia['plazo'],
                        'estado'         => $referencia['estado']
                    ];
                    $this->refCrediticias->insert($data);
                } catch (\Throwable $th) {
                    $errores[] = [
                        'index' => $index,
                        'error' => $th->getMessage(),
                        'referencia' => $referencia
                    ];
                }
            }
        }
        return $errores;  // Asegúrate de que siempre retorne el array de errores.
    }


    private function crearEstudioSocioEconomico(array $analisis_socioeconomico, int $idSolicitud, $idCliente): bool
    {
        try {
            // Asignar valores, reemplazar vacíos por 0
            $ingresoMensual = !empty($analisis_socioeconomico['ingresoMensual']) ? $analisis_socioeconomico['ingresoMensual'] : 0;
            $egresoMensual = !empty($analisis_socioeconomico['egresoMensual']) ? $analisis_socioeconomico['egresoMensual'] : 0;
            $salarioIng = !empty($analisis_socioeconomico['salarioIng']) ? $analisis_socioeconomico['salarioIng'] : 0;
            $pagoCasa = !empty($analisis_socioeconomico['pagoCasa']) ? $analisis_socioeconomico['pagoCasa'] : 0;
            $otrosIngresos = !empty($analisis_socioeconomico['otrosIngresos']) ? $analisis_socioeconomico['otrosIngresos'] : 0;
            $gastosVida = !empty($analisis_socioeconomico['gastosVida']) ? $analisis_socioeconomico['gastosVida'] : 0;
            $otrosEgresos = !empty($analisis_socioeconomico['otrosEgresos']) ? $analisis_socioeconomico['otrosEgresos'] : 0;
            $totalIngresos = !empty($analisis_socioeconomico['totalIngresos']) ? $analisis_socioeconomico['totalIngresos'] : 0;
            $totalEgresos = !empty($analisis_socioeconomico['totalEgresos']) ? $analisis_socioeconomico['totalEgresos'] : 0;
            $diferencia = !empty($analisis_socioeconomico['diferencia']) ? $analisis_socioeconomico['diferencia'] : 0;
            $estadoLabel = !empty($analisis_socioeconomico['estado_label']) ? $analisis_socioeconomico['estado_label'] : '';

            $dataEstudio = [
                'id_solicitud'                 => $idSolicitud,
                'ingreso_mensual'              => $ingresoMensual,
                'egreso_mensual'               => $egresoMensual,
                'salario'                      => $salarioIng,
                'pago_casa'                    => $pagoCasa,
                'otros_explicacion'            => $otrosIngresos,
                'gastos_vida'                  => $gastosVida,
                'otros'                        => $otrosEgresos,
                'total_ingresos'               => $totalIngresos,
                'total_egresos'                => $totalEgresos,
                'diferencia_ingresos_egresos'  => $diferencia,
                'estado_financiero'            => $estadoLabel,
                'id_cliente'                   => $idCliente
            ];

            $this->estudioSocioEconomicoModel->insert($dataEstudio);

            return true;
        } catch (\Throwable $th) {
            log_message('error', 'Error al crear el estudio socioeconómico: ' . $th->getMessage());
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

    private function crearPlanPago(array $dataPlanPago, int $idSolicitud): bool
    {
        try {
            // Preparar los datos para la inserción
            $data = [
                'valor_articulo'     => $dataPlanPago['valorArticulo'],
                'valor_prima'        => $dataPlanPago['valorPagoPrima'],
                'saldo_a_pagar'      => $dataPlanPago['saldoAPagar'],
                'cuotas'             => $dataPlanPago['cantidadCuotas'],
                'monto_cuotas'       => $dataPlanPago['montoCuota'],
                'monto_total_pagar'  => $dataPlanPago['montoTotalPagar'],
                'observaciones'      => isset($dataPlanPago['observaciones']) ? $dataPlanPago['observaciones'] : "", // Puedes ajustar esto según sea necesario
                'id_solicitud'       => $idSolicitud
            ];

            // Insertar los datos usando el modelo
            $this->planDePagoModel->insert($data);

            return true;
        } catch (\Throwable $th) {
            log_message('error', 'Error al crear el plan de pago: ' . $th->getMessage());
            return false;
        }
    }

    public function crearCodeudor($data, $idSolicitud): bool
    {
        $codeudorData = [
            'nombre' => $data['nombre'],
            'dui' => $data['dui'],
            'direccion' => $data['direccion'],
            'telefono_personal' => $data['COtelPersonal'],
            'vive_en_casa_propia' => $data['COpropiaCN'],
            'en_promesa_de_venta' => $data['COpromesaVenta'],
            'alquilada' => $data['COalquilada'],
            'tiempo' => $data['COtiempoVivienda'],
            'estado_civil' => $data['COestadoCivil'],
            'nombre_conyugue' => $data['nombreConyugueCN'],
            'profesion_oficio' => $data['profesion'],
            'patrono_empresa' => $data['patrono'],
            'direccion_trabajo' => $data['COdireccionTrabajo'],
            'telefono_trabajo' => $data['COtelefonoTrabajo'],
            'cargo' => $data['COcargoDesempeña'],
            'salario' => $data['COsalario'],
            'nombre_jefe_inmediato' => $data['COnombreJefe'],
            'id_solicitud' => $idSolicitud
        ];

        $this->codeudorModel->insert($codeudorData);
        $idCodeudor = $this->codeudorModel->insertID();

        if ($idCodeudor) {
            if (!empty($data['referencias'])) {
                foreach ($data['referencias'] as $referencia) {
                    $referenciaData = [
                        'id_codeudor' => $idCodeudor,
                        'nombre' => $referencia['nombre'],
                        'parentesco' => $referencia['parentesco'],
                        'direccion' => $referencia['direccion'],
                        'telefono' => $referencia['telefono']
                    ];
                    $this->refcodeudorModel->insert($referenciaData);
                }
            }
            return true;
        } else {
            throw new \Exception("Error al guardar el codeudor.");
        }
    }


    public function registrarMovimientoSalidaVenta($datos, $id_solicitud, $numSol): bool
    {
        try {
            log_message("info", $this->nameClass . " registrarMovimientoSalidaVenta");
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
                        'id_documento' => null // Es null porque se realizó una salida por venta, o sea por solicitud
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


    public function ver_solicitud()
    {
        try {
            $session = session();
            if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
                $request = service('request');
                $encryptedSolicitud = $request->getGet('solicitud');
                log_message("info", "encryptedSolicitud::: " . $encryptedSolicitud);

                if ($encryptedSolicitud) {
                    $id_solicitud = base64_decode($encryptedSolicitud);
                    log_message("info", "numero de solicitud decrypted::: " . $id_solicitud);

                    # paso 1: recupero la información de la solicitud
                    $solicitudEncontrada = $this->solicitudesModel->find($id_solicitud);
                    # paso 2: recupero el cliente asociado a la solicitud desde el modelo de clientes
                    $clienteEncontrado = $this->clientesModel->buscarCliente(null, (int) $solicitudEncontrada['id_cliente']);

                    // Buscar los nombres del departamento y municipio utilizando sus códigos
                    $departamentoCliente = $this->deptoModel->getDepartamentoPorCodigo($clienteEncontrado['departamento']);
                    $municipioCliente = $this->muniModel->getMunicipioPorCodigo($clienteEncontrado['municipio']);
                    $coloniaCliente = $this->coloniaModel->getColoniasByCliente($clienteEncontrado['colonia']);

                    log_message("info", "Nombre de la colonia cliente: " . $coloniaCliente);
                    # paso 3: se recuperan las referencias laborales del cliente
                    $refLaboralEncontrado = $this->refLaboralModel->obtenerReferenciasPorSolicitud($id_solicitud);
                    log_message("info", "Datos de la refLaboral:: " . print_r($refLaboralEncontrado, true));

                    # paso 4: se recuperan las ref familires
                    $refFamiliares = $this->refFamiliresModel->buscarPorSolicitud($id_solicitud);

                    # paso 5: se recuperan las referencias no familiares
                    $refNoFamiliares = $this->refNoFamiliresModel->buscarPorSolicitud($id_solicitud);

                    # paso 6: se recuperan las ref crediticias
                    $refCrediticias = $this->refCrediticias->buscarPorSolicitud($id_solicitud);

                    # paso 7: se recupera el estudio socieconomico
                    $estSocioEconomico = $this->estudioSocioEconomicoModel->buscarPorSolicitud($id_solicitud);

                    # paso 8: recupera articulo x solicitud
                    $productosSolicitud = $this->prodSolicitudModel->buscarPorSolicitud($id_solicitud);

                    # paso 9: plan de pago
                    // falta la observacion
                    $planPago = $this->planDePagoModel->buscarPorSolicitud($id_solicitud);

                    # paso 10: se traen los datos del codeudor
                    $codeudor = $this->codeudorModel->buscarPorSolicitud($id_solicitud);

                    $refCodeudor = $this->refcodeudorModel->buscarPorCodeudor($codeudor[0]['id_codeudor']);

                    # Ultimo paso: se guardaran todos los resultado
                    $datosSolicitud = [
                        'perfil' => $_SESSION['perfilN'],
                        'cliente' => $clienteEncontrado,
                        'deptClienteN' => $departamentoCliente,
                        'muniClienteN' => $municipioCliente,
                        'coloniaCliente' => $coloniaCliente,
                        'refLaboral' => $refLaboralEncontrado,
                        'refFamiliares' => $refFamiliares,
                        'refNoFamiliares' => $refNoFamiliares,
                        'refCrediticia' => $refCrediticias,
                        'analisisSocioeconomico' => $estSocioEconomico,
                        'productosSol' => $productosSolicitud,
                        'planPago' => $planPago,
                        'codeudor' => $codeudor,
                        'refCodeudor' => $refCodeudor,
                        'observacionSol' => !empty($solicitudEncontrada['observacion']) ? $solicitudEncontrada['observacion'] : "",
                        'id_estado_actual' => $solicitudEncontrada['id_estado_actual']
                    ];
                    $content4 = view('solicitudes/ver_solicitud', $datosSolicitud);
                    $fullPage = $this->renderPage($content4);
                    return $fullPage;
                } else {
                    // Manejar el error cuando el parámetro no está presente o es inválido
                    return $this->response->setStatusCode(400)->setBody("Solicitud no válida.");
                }
            } else {
                return redirect()->to(base_url());
            }
        } catch (\Throwable $e) {
            $errorMessage = 'Ocurrió un error: ' . $e->getMessage() . PHP_EOL;
            $errorMessage .= 'Trace: ' . $e->getTraceAsString();

            log_message('error', $errorMessage);
            return false;
        }
    }

    public function copy_solicitud()
    {
        try {
            $session = session();
            if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
                $request = service('request');
                $encryptedSolicitud = $request->getGet('solicitud');
                log_message("info", "encryptedSolicitud::: " . $encryptedSolicitud);

                if ($encryptedSolicitud) {
                    $id_solicitud = base64_decode($encryptedSolicitud);
                    log_message("info", "numero de solicitud decrypted::: " . $id_solicitud);

                    # paso 1: recupero la información de la solicitud
                    $solicitudEncontrada = $this->solicitudesModel->find($id_solicitud);
                    # paso 2: recupero el cliente asociado a la solicitud desde el modelo de clientes
                    $clienteEncontrado = $this->clientesModel->buscarCliente(null, (int) $solicitudEncontrada['id_cliente']);

                    // Buscar los nombres del departamento y municipio utilizando sus códigos
                    $departamentoCliente = $this->deptoModel->getDepartamentoPorCodigo($clienteEncontrado['departamento']);
                    $municipioCliente = $this->muniModel->getMunicipioPorCodigo($clienteEncontrado['municipio']);
                    $distritoCliente = $this->distritoModel->getDistritosBId($clienteEncontrado['distrito']);
                    $coloniaCliente = $this->coloniaModel->getColoniasByCliente($clienteEncontrado['colonia']);
                    
                    # paso 3: se recuperan las referencias laborales del cliente
                    $refLaboralEncontrado = $this->refLaboralModel->obtenerReferenciasPorSolicitud($id_solicitud);

                    # paso 4: se recuperan las ref familires
                    $refFamiliares = $this->refFamiliresModel->buscarPorSolicitud($id_solicitud);

                    # paso 5: se recuperan las referencias no familiares
                    $refNoFamiliares = $this->refNoFamiliresModel->buscarPorSolicitud($id_solicitud);

                    # paso 6: se recuperan las ref crediticias
                    $refCrediticias = $this->refCrediticias->buscarPorSolicitud($id_solicitud);

                    # paso 7: se recupera el estudio socieconomico
                    $estSocioEconomico = $this->estudioSocioEconomicoModel->buscarPorSolicitud($id_solicitud);

                    # paso 8: recupera articulo x solicitud
                    $productosSolicitud = $this->prodSolicitudModel->buscarPorSolicitud($id_solicitud);

                    # paso 9: plan de pago
                    // falta la observacion
                    //$planPago = $this->planDePagoModel->buscarPorSolicitud($id_solicitud);

                    # paso 10: se traen los datos del codeudor
                    $codeudor = $this->codeudorModel->buscarPorSolicitud($id_solicitud);

                    $refCodeudor = $this->refcodeudorModel->buscarPorCodeudor($codeudor[0]['id_codeudor']);

                    # Ultimo paso: se guardaran todos los resultado
                    $datosSolicitud = [
                        'perfil' => $_SESSION['perfilN'],
                        'cliente' => $clienteEncontrado,
                        'deptClienteN' => $departamentoCliente,
                        'muniClienteN' => $municipioCliente,
                        'distritoN' => $distritoCliente,
                        'coloniaCliente' => $coloniaCliente,
                        'refLaboral' => $refLaboralEncontrado,
                        'refFamiliares' => $refFamiliares,
                        'refNoFamiliares' => $refNoFamiliares,
                        'refCrediticia' => $refCrediticias,
                        'analisisSocioeconomico' => $estSocioEconomico,
                        'productosSol' => $productosSolicitud,
                        'codeudor' => $codeudor,
                        'refCodeudor' => $refCodeudor,
                        'observacionSol' => !empty($solicitudEncontrada['observacion']) ? $solicitudEncontrada['observacion'] : "",
                        'id_estado_actual' => $solicitudEncontrada['id_estado_actual']
                    ];
                    $content4 = view('solicitudes/copiar_solicitud', $datosSolicitud);
                    $fullPage = $this->renderPage($content4);
                    return $fullPage;
                } else {
                    // Manejar el error cuando el parámetro no está presente o es inválido
                    return $this->response->setStatusCode(400)->setBody("Solicitud no válida.");
                }
            } else {
                return redirect()->to(base_url());
            }
        } catch (\Throwable $e) {
            $errorMessage = 'Ocurrió un error: ' . $e->getMessage() . PHP_EOL;
            $errorMessage .= 'Trace: ' . $e->getTraceAsString();

            log_message('error', $errorMessage);
            return false;
        }
    }

    public function actualizarEstado()
    {
        try {
            $id_estado = $this->request->getPost('id_estado');
            $observacion = $this->request->getPost('observacion');
            $id_solicitud = $this->request->getPost('id_solicitud');

            if ($id_solicitud && !$id_estado && !$observacion) {
                // Obtienes la solicitud
                $numeroSoli = $this->solicitudesModel->find($id_solicitud);

                // Primero, validamos si el contrato ya existe
                $rspContratoValidado = $this->generarDocController->validarContrato($numeroSoli['numero_solicitud']);
                log_message("info", "Validando si el estado existe:: " . print_r($rspContratoValidado, true));

                if ($rspContratoValidado['success'] && $rspContratoValidado['message'] == 'existe') {
                    log_message("info", "Entra en el if del contrato generado");
                    // Si el contrato ya existe, seguimos con el flujo, pero no generamos el contrato nuevamente
                    $rspContrato = [
                        'success' => true,
                        'message' => 'El contrato ya existe para esta solicitud.',
                        'solicitud' => $numeroSoli['numero_solicitud']
                    ];
                } else {
                    // Si el contrato no existe, generamos el contrato
                    log_message("info", "Entra en el else de generar contrato");
                    $rspContrato = $this->generarDocController->generarContrato($id_solicitud);
                }

                log_message("info", "valor de rspContrato:: " . print_r($rspContrato, true));

                // Aquí continuamos con el flujo después de validar o generar el contrato
                if ($rspContrato['success']) {
                    log_message("info", "Entro al if2 actualizarEstado");

                    // Actualizamos el estado de la solicitud
                    $dataUpdate = [
                        'id_estado_actual' => 2
                    ];

                    // Si se actualiza el estado correctamente
                    if ($this->solicitudesModel->update($id_solicitud, $dataUpdate)) {
                        log_message('info', '*********************************** COBROS ***********************************');
                        $planPago = $this->planDePagoModel->buscarPorSolicitud($id_solicitud);
                        $this->crearCobros($planPago, $id_solicitud);
                        log_message('info', '********************************* FIN COBROS *********************************');
                    } else {
                        log_message("info", "no se actualizo");
                    }

                    // Retornamos la respuesta con el mensaje de éxito
                    return $this->response->setJSON([
                        'success' => true,
                        'message' => $rspContrato['message'],
                        'solicitud' => $rspContrato['solicitud']
                    ]);
                } else {
                    // Si ocurrió un error al generar el contrato
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => $rspContrato['message']
                    ]);
                }
            } else if ($id_solicitud && $id_estado && $observacion) {
                log_message("info", "Entro al else actualizarEstado");
                log_message('info', 'Datos recibidos: id_estado: {id_estado}, observacion: {observacion}, id_solicitud: {id_solicitud}', [
                    'id_estado' => $id_estado,
                    'observacion' => $observacion,
                    'id_solicitud' => $id_solicitud
                ]);
                $data = [
                    'id_estado_actual' => $id_estado,
                    'observacion' => $observacion
                ];

                $updated = $this->solicitudesModel->update($id_solicitud, $data);
                if ($updated) {
                    return $this->response->setJSON([
                        'success' => true,
                        'message' => 'Estado y observación actualizados correctamente'
                    ]);
                } else {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Error al actualizar la solicitud'
                    ]);
                }
            }

            return $this->response->setJSON(['success' => 'Estado actualizado correctamente']);
        } catch (\Throwable $e) {
            $errorMessage = 'Ocurrió un error: ' . $e->getMessage() . PHP_EOL;
            $errorMessage .= 'Trace: ' . $e->getTraceAsString();

            log_message('error', $errorMessage);

            return $this->response->setJSON(['error' => 'Error en al procesar la documentacion']);
        }
    }

    /* private function crearCobros($planPago, $id_solicitud)
    {
        try {
            $solicitudEncontrada = $this->solicitudesModel->find($id_solicitud);
            $fechaCompleta = $solicitudEncontrada["fecha_creacion"];
            $fechaSol = explode(" ", $fechaCompleta)[0];
            $fecha = new \DateTime($fechaSol);

            $cantMeses = $planPago[0]["cuotas"];
            for ($i = 0; $i <= $cantMeses; $i++) {
                $fechaPago = clone $fecha;

                if ($i != 0) {
                    $fechaPago->modify("+{$i} month");
                }
                $fechaVencimiento = clone $fechaPago;
                //$fechaVencimiento->modify("+1 day");

                $data = [
                    'id_solicitud'      => $id_solicitud,
                    'numero_cuota'      => $i,
                    'monto_cuota'       => $i == 0 ? $planPago[0]["valor_prima"] : $planPago[0]["monto_cuotas"],
                    'descripcion'       => $i == 0 ? "Pago de prima o cuota numero " . $i . " de " . $planPago[0]["cuotas"] : "",
                    'estado'            => $i == 0 ? "CANCELADO" : "PENDIENTE",
                    'fecha_pago'        => "",
                    'fecha_vencimiento' => $fechaVencimiento->format("Y-m-d")
                ];

                if($this->cobrosModel->insert($data)){
                    log_message('info', "Guardado");
                }else {
                    log_message('info', "no guardado");
                }
            }
        } catch (\Throwable $e) {
            $errorMessage = 'Ocurrió un error: ' . $e->getMessage() . PHP_EOL;
            $errorMessage .= 'Trace: ' . $e->getTraceAsString();
            log_message('error', $errorMessage);
            return $this->response->setJSON(['error' => 'Error al procesar la generación de cobros']);
        }
    } */
    private function crearCobros($planPago, $id_solicitud)
    {
        try {
            $solicitudEncontrada = $this->solicitudesModel->find($id_solicitud);
            $fechaCompleta = $solicitudEncontrada["fecha_creacion"];
            $fechaSol = explode(" ", $fechaCompleta)[0];
            $fecha = new \DateTime($fechaSol);

            $cantMeses = $planPago[0]["cuotas"];
            for ($i = 0; $i <= $cantMeses; $i++) {
                $fechaPago = clone $fecha;
                // Validar si el valor de la prima es 0
                $esPagoPrima = $i == 0 && $planPago[0]["valor_prima"] != 0;
                /* if ($i != 0) { */
                if (!$esPagoPrima) {
                    $fechaPago->modify("+{$i} month");
                }
                $fechaVencimiento = clone $fechaPago;

                // Construir los datos del cobro
                $data = [
                    'id_solicitud'      => $id_solicitud,
                    'numero_cuota'      => $i,
                    'monto_cuota'       => $esPagoPrima ? $planPago[0]["valor_prima"] : $planPago[0]["monto_cuotas"],
                    'descripcion'       => $esPagoPrima ? "Pago de prima o cuota numero " . $i . " de " . $planPago[0]["cuotas"] : "",
                    'estado'            => $esPagoPrima ? "CANCELADO" : "PENDIENTE",
                    'fecha_pago'        => "",
                    'fecha_vencimiento' => $fechaVencimiento->format("Y-m-d"),
                    'esPrima'           => $esPagoPrima ? 1 : 0
                ];

                // Evitar registrar una cuota de prima si su valor es 0
                if ($i != 0 || $planPago[0]["valor_prima"] != 0) {
                    if ($this->cobrosModel->insert($data)) {
                        log_message('info', "Guardado");
                    } else {
                        log_message('info', "No guardado");
                    }
                }
            }
        } catch (\Throwable $e) {
            $errorMessage = 'Ocurrió un error: ' . $e->getMessage() . PHP_EOL;
            $errorMessage .= 'Trace: ' . $e->getTraceAsString();
            log_message('error', $errorMessage);
            return $this->response->setJSON(['error' => 'Error al procesar la generación de cobros']);
        }
    }

    public function buscarReferencias()
    {
        try {
            log_message('info', 'Iniciando búsqueda de referencias.');

            // Obtén el ID del cliente desde el POST
            $idCliente = $this->request->getPost('idCliente');
            log_message('info', 'ID del cliente recibido: {idCliente}', ['idCliente' => $idCliente]);

            // Obtén la última solicitud del cliente
            $solicitudEncontrada = $this->solicitudesModel->getUltimaSolicitudCliente($idCliente);
            log_message('info', 'Solicitud encontrada: {solicitud}', ['solicitud' => json_encode($solicitudEncontrada)]);

            if (empty($solicitudEncontrada)) {
                log_message('warning', 'No se encontró ninguna solicitud para el cliente: {idCliente}', ['idCliente' => $idCliente]);
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No se encontraron datos para cargar referencias.',
                ]);
            }

            // Extrae el ID de la solicitud
            $id_solicitud = $solicitudEncontrada['id_solicitud'];
            log_message('info', 'ID de solicitud extraído: {id_solicitud}', ['id_solicitud' => $id_solicitud]);

            // Obtén las referencias laborales, familiares y no familiares
            $refLaboralEncontrado = $this->refLaboralModel->obtenerReferenciasPorSolicitud($id_solicitud);
            log_message('info', 'Referencias laborales encontradas: {refLaboral}', ['refLaboral' => json_encode($refLaboralEncontrado)]);

            $refFamiliares = $this->refFamiliresModel->buscarPorSolicitud($id_solicitud);
            log_message('info', 'Referencias familiares encontradas: {refFamiliares}', ['refFamiliares' => json_encode($refFamiliares)]);

            $refNoFamiliares = $this->refNoFamiliresModel->buscarPorSolicitud($id_solicitud);
            log_message('info', 'Referencias no familiares encontradas: {refNoFamiliares}', ['refNoFamiliares' => json_encode($refNoFamiliares)]);

            // Estructura la respuesta JSON
            return $this->response->setJSON([
                'success' => true,
                'referenciaLaboral' => [
                    'data' => $refLaboralEncontrado,
                    'success' => !empty($refLaboralEncontrado),
                ],
                'referenciaFamiliar' => [
                    'data' => $refFamiliares,
                    'success' => !empty($refFamiliares),
                ],
                'referenciaNoFamiliar' => [
                    'data' => $refNoFamiliares,
                    'success' => !empty($refNoFamiliares),
                ],
            ]);
        } catch (\Throwable $e) {
            // Manejo de errores
            log_message('error', 'Ocurrió un error en la búsqueda de referencias: {error}', ['error' => $e->getMessage()]);
            log_message('error', 'Trace del error: {trace}', ['trace' => $e->getTraceAsString()]);

            // Respuesta de error en JSON
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al procesar la búsqueda de referencias.',
                'error' => $e->getMessage(),
            ]);
        }
    }
}
