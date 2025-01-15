<?php

namespace App\Controllers;

use PhpOffice\PhpWord\TemplateProcessor;
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
use App\Models\ContratoSolicitudModel;
use App\Models\ContratoModel;
use App\Models\ApoderadosModel;
use App\Controllers\ConvertidoresController;

class GenerarSolicitudCreditoController extends BaseController
{
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
    private $contratoSolicitud;
    protected $contratoModel;
    protected $apoderadoModel;
    protected $convertidoresController;

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
        $this->muniModel = new MunicipiosModel();
        $this->contratoSolicitud = new ContratoSolicitudModel();
        $this->contratoModel = new ContratoModel();
        $this->apoderadoModel = new ApoderadosModel();
        $this->convertidoresController = new ConvertidoresController();
    }

    public function generarDocumento()
    {
        try {
            $request = service('request');
            $encryptedSolicitud = $request->getGet('solicitud');
            if ($encryptedSolicitud) {
                log_message("info", "generarDocumento encryptedSolicitud::: " . $encryptedSolicitud);
                $templatePath = FCPATH . 'public\documentos\solicitud_credito\solicitudCredito.docx';

                $id_solicitud = base64_decode($encryptedSolicitud);

                if (!file_exists($templatePath)) {
                    throw new \Exception('El archivo de plantilla no se encuentra en la ruta especificada.');
                }
                $templateProcessor = new TemplateProcessor($templatePath);

                $solicitudEncontrada = $this->solicitudesModel->find($id_solicitud);
                # paso 2: recupero el cliente asociado a la solicitud desde el modelo de clientes
                $clienteEncontrado = $this->clientesModel->buscarCliente(null, $solicitudEncontrada['id_cliente']);

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
                $planPago = $this->planDePagoModel->buscarPorSolicitud($id_solicitud);

                # paso 10: se traen los datos del codeudor
                $codeudor = $this->codeudorModel->buscarPorSolicitud($id_solicitud);
                $refCodeudor = $this->refcodeudorModel->buscarPorCodeudor($codeudor[0]['id_codeudor']);

                $datos = [
                    //Datos del cliente
                    'nombreC' => !empty($clienteEncontrado['nombre_completo']) ? $clienteEncontrado['nombre_completo'] : '',
                    'duiC' => !empty($clienteEncontrado['dui']) ? $clienteEncontrado['dui'] : '',
                    'edadC' => !empty($clienteEncontrado['fecha_nacimiento']) ? (string)(date('Y') - date('Y', strtotime($clienteEncontrado['fecha_nacimiento']))) . ' AÑOS' : '',
                    'direccionActual' => !empty($clienteEncontrado['direccion']) ? $clienteEncontrado['direccion'] : '',
                    'telC' => !empty($clienteEncontrado['telefono']) ? $clienteEncontrado['telefono'] : '',
                    'casaPropia' => !empty($clienteEncontrado['CpropiaCN']) ? $clienteEncontrado['CpropiaCN'] : '',
                    'promVenta' => !empty($clienteEncontrado['CpromesaVentaCN']) ? $clienteEncontrado['CpromesaVentaCN'] : '',
                    'alquilada' => !empty($clienteEncontrado['CalquiladaCN']) ? $clienteEncontrado['CalquiladaCN'] : '',
                    'aQuien' => !empty($clienteEncontrado['aQuienPerteneceCN']) ? $clienteEncontrado['aQuienPerteneceCN'] : 'N/A',
                    'telPropietario' => !empty($clienteEncontrado['telPropietarioCN']) ? $clienteEncontrado['telPropietarioCN'] : 'N/A',
                    'tiempoVDomicilio' => !empty($clienteEncontrado['tiempoDeVivirDomicilioCN']) ? $clienteEncontrado['tiempoDeVivirDomicilioCN'] : '',
                    'estadoCivil' => !empty($clienteEncontrado['estado_civil']) ? $clienteEncontrado['estado_civil'] : '',
                    'nombreConyugue' => !empty($clienteEncontrado['nombre_conyugue']) ? $clienteEncontrado['nombre_conyugue'] : '',
                    'dirConyugue' => !empty($clienteEncontrado['direccion_trabajo_conyugue']) ? $clienteEncontrado['direccion_trabajo_conyugue'] : '',
                    'telConyugue' => !empty($clienteEncontrado['telefono_trabajo_conyugue']) ? $clienteEncontrado['telefono_trabajo_conyugue'] : '',
                    'nombrePoM' => !empty($clienteEncontrado['nombre_padres']) ? $clienteEncontrado['nombre_padres'] : '',
                    'direccionPadre' => !empty($clienteEncontrado['direccion_padres']) ? $clienteEncontrado['direccion_padres'] : '',
                    'dirAnteriorC' => !empty($clienteEncontrado['direccionAnteior']) ? $clienteEncontrado['direccionAnteior'] : '',
                    'telPadres' => !empty($clienteEncontrado['telefono_padres']) ? $clienteEncontrado['telefono_padres'] : '',

                    'fechaSol' => date('Y-m-d', strtotime($solicitudEncontrada['fecha_creacion'])),
                    //Referencias laborales
                    'profesionClienteRef' => isset($refLaboralEncontrado[0]['profesion_oficio']) ? $refLaboralEncontrado[0]['profesion_oficio'] : '',
                    'lugarTrabajoRef' => isset($refLaboralEncontrado[0]['empresa']) ? $refLaboralEncontrado[0]['empresa'] : '',
                    'dirTrabajoRef' => isset($refLaboralEncontrado[0]['direccion_trabajo']) ? $refLaboralEncontrado[0]['direccion_trabajo'] : '',
                    'telTrabajoRef' => isset($refLaboralEncontrado[0]['telefono_trabajo']) ? $refLaboralEncontrado[0]['telefono_trabajo'] : '',
                    'cargoDesempeñaRef' => isset($refLaboralEncontrado[0]['cargo']) ? $refLaboralEncontrado[0]['cargo'] : '',
                    'salarioRef' => isset($refLaboralEncontrado[0]['salario']) ? $refLaboralEncontrado[0]['salario'] : '',
                    'tiempoLaboralRef' => isset($refLaboralEncontrado[0]['tiempo_laborado_empresa']) ? $refLaboralEncontrado[0]['tiempo_laborado_empresa'] : '',
                    'jefeInmediatoRef' => isset($refLaboralEncontrado[0]['nombre_jefe_inmediato']) ? $refLaboralEncontrado[0]['nombre_jefe_inmediato'] : '',
                    'empresaAnterior' => isset($refLaboralEncontrado[0]['empresa_anterior']) ? $refLaboralEncontrado[0]['empresa_anterior'] : '',
                    'telEmpRef' => isset($refLaboralEncontrado[0]['telefono_empresa_anterior']) ? $refLaboralEncontrado[0]['telefono_empresa_anterior'] : '',

                    'nomRefFam1' => isset($refFamiliares[0]['nombre']) ? $refFamiliares[0]['nombre'] : '',
                    'parentescoRefFam1' => isset($refFamiliares[0]['parentesco']) ? $refFamiliares[0]['parentesco'] : '',
                    'direccionRefFam1' => isset($refFamiliares[0]['direccion']) ? $refFamiliares[0]['direccion'] : '',
                    'telRef1' => isset($refFamiliares[0]['telefono']) ? $refFamiliares[0]['telefono'] : '',
                    'lugarTrabajoRefFam1' => isset($refFamiliares[0]['lugar_trabajo']) ? $refFamiliares[0]['lugar_trabajo'] : '',
                    'telTrabajoRefFam1' => isset($refFamiliares[0]['telefono_trabajo']) ? $refFamiliares[0]['telefono_trabajo'] : '',

                    'nomRefFam2' => isset($refFamiliares[1]['nombre']) ? $refFamiliares[1]['nombre'] : '',
                    'parentescoRefFam2' => isset($refFamiliares[1]['parentesco']) ? $refFamiliares[1]['parentesco'] : '',
                    'direccionRefFam2' => isset($refFamiliares[1]['direccion']) ? $refFamiliares[1]['direccion'] : '',
                    'telRef2' => isset($refFamiliares[1]['telefono']) ? $refFamiliares[1]['telefono'] : '',
                    'lugarTrabajoRefFam2' => isset($refFamiliares[1]['lugar_trabajo']) ? $refFamiliares[1]['lugar_trabajo'] : '',
                    'telTrabajoRefFam2' => isset($refFamiliares[1]['telefono_trabajo']) ? $refFamiliares[1]['telefono_trabajo'] : '',

                    //no fam
                    'nomRefNoFam1' => isset($refNoFamiliares[0]['nombre']) ? $refNoFamiliares[0]['nombre'] : '',
                    'direccionRefNoFam1' => isset($refNoFamiliares[0]['direccion']) ? $refNoFamiliares[0]['direccion'] : '',
                    'telRefNoFam1' => isset($refNoFamiliares[0]['telefono']) ? $refNoFamiliares[0]['telefono'] : '',
                    'lugarTrabajoRefNoFam1' => isset($refNoFamiliares[0]['lugar_trabajo']) ? $refNoFamiliares[0]['lugar_trabajo'] : '',
                    'telTrabajoRefNoFam1' => isset($refNoFamiliares[0]['telefono_trabajo']) ? $refNoFamiliares[0]['telefono_trabajo'] : '',

                    'nomRefNoFam2' => isset($refNoFamiliares[1]['nombre']) ? $refNoFamiliares[1]['nombre'] : '',
                    'direccionRefNoFam2' => isset($refNoFamiliares[1]['direccion']) ? $refNoFamiliares[1]['direccion'] : '',
                    'telRefNoFam2' => isset($refNoFamiliares[1]['telefono']) ? $refNoFamiliares[1]['telefono'] : '',
                    'lugarTrabajoRefNoFam2' => isset($refNoFamiliares[1]['lugar_trabajo']) ? $refNoFamiliares[1]['lugar_trabajo'] : '',
                    'telTrabajoRefNoFam2' => isset($refNoFamiliares[1]['telefono_trabajo']) ? $refNoFamiliares[1]['telefono_trabajo'] : '',
                    //ref creditica
                    'almacenRefC1' => isset($refCrediticias[0]['institucion']) ? $refCrediticias[0]['institucion'] : '',
                    'telRefC1' => isset($refCrediticias[0]['telefono']) ? $refCrediticias[0]['telefono'] : '',
                    'montoCreditoRefC1' => isset($refCrediticias[0]['monto_credito']) ? $refCrediticias[0]['monto_credito'] . ' USD' : '',
                    'periodosRefC1' => isset($refCrediticias[0]['periodos']) ? $refCrediticias[0]['periodos'] : '',
                    'plazoCreditoRef1' => isset($refCrediticias[0]['plazo']) ? $refCrediticias[0]['plazo'] : '',
                    'estadoCreditoRef1' => isset($refCrediticias[0]['estado']) ? $refCrediticias[0]['estado'] : '',

                    'almacenRefC2' => isset($refCrediticias[1]['institucion']) ? $refCrediticias[1]['institucion'] : '',
                    'telRefC2' => isset($refCrediticias[1]['telefono']) ? $refCrediticias[1]['telefono'] : '',
                    'montoCreditoRefC2' => isset($refCrediticias[1]['monto_credito']) ? $refCrediticias[1]['monto_credito'] . ' USD' : '',
                    'periodosRefC2' => isset($refCrediticias[1]['periodos']) ? $refCrediticias[1]['periodos'] : '',
                    'plazoCreditoRef2' => isset($refCrediticias[1]['plazo']) ? $refCrediticias[1]['plazo'] : '',
                    'estadoCreditoRef2' => isset($refCrediticias[1]['estado']) ? $refCrediticias[1]['estado'] : '',

                    'almacenRefC3' => isset($refCrediticias[2]['institucion']) ? $refCrediticias[2]['institucion'] : '',
                    'telRefC3' => isset($refCrediticias[2]['telefono']) ? $refCrediticias[2]['telefono'] : '',
                    'montoCreditoRefC3' => isset($refCrediticias[2]['monto_credito']) ? $refCrediticias[2]['monto_credito'] . ' USD' : '',
                    'periodosRefC3' => isset($refCrediticias[2]['periodos']) ? $refCrediticias[2]['periodos'] : '',
                    'plazoCreditoRef3' => isset($refCrediticias[2]['plazo']) ? $refCrediticias[2]['plazo'] : '',
                    'estadoCreditoRef3' => isset($refCrediticias[2]['estado']) ? $refCrediticias[2]['estado'] : '',
                    //socieconomico
                    'ingresoMensual' => isset($estSocioEconomico[0]['ingreso_mensual']) ? $estSocioEconomico[0]['ingreso_mensual'] : '0.00',
                    'egresoMensual' => isset($estSocioEconomico[0]['egreso_mensual']) ? $estSocioEconomico[0]['egreso_mensual'] : '0.00',
                    'salario' => isset($estSocioEconomico[0]['salario']) ? $estSocioEconomico[0]['salario'] : '0.00',
                    'pagoCasa' => isset($estSocioEconomico[0]['pago_casa']) ? $estSocioEconomico[0]['pago_casa'] : '0.00',
                    'otrosExpli' => isset($estSocioEconomico[0]['otros_explicacion']) ? $estSocioEconomico[0]['otros_explicacion'] : '',
                    'gastoVida' => isset($estSocioEconomico[0]['gastos_vida']) ? $estSocioEconomico[0]['gastos_vida'] : '0.00',
                    'otrosEgresos' => isset($estSocioEconomico[0]['otros']) ? $estSocioEconomico[0]['otros'] : '0.00',
                    'totalIngreso' => isset($estSocioEconomico[0]['total_ingresos']) ? $estSocioEconomico[0]['total_ingresos'] : '0.00',
                    'totalEgreso' => isset($estSocioEconomico[0]['total_egresos']) ? $estSocioEconomico[0]['total_egresos'] : '0.00',
                    'diferencia' => isset($estSocioEconomico[0]['diferencia_ingresos_egresos']) ? $estSocioEconomico[0]['diferencia_ingresos_egresos'] : '0.00',

                    'pM1' => isset($productosSolicitud[0]) && isset($productosSolicitud[0]['marca']) ? $productosSolicitud[0]['marca'] : '',
                    'pMod1' => isset($productosSolicitud[0]) && isset($productosSolicitud[0]['modelo']) ? $productosSolicitud[0]['modelo'] : '',
                    'pCol1' => isset($productosSolicitud[0]) && isset($productosSolicitud[0]['color']) ? $productosSolicitud[0]['color'] : '',
                    'pCod1' => isset($productosSolicitud[0]) && isset($productosSolicitud[0]['codigo_producto']) ? $productosSolicitud[0]['codigo_producto'] : '',
                    'pPre1' => isset($productosSolicitud[0]) && isset($productosSolicitud[0]['precio']) ? $productosSolicitud[0]['precio'] : '',

                    'pM2' => isset($productosSolicitud[1]) && isset($productosSolicitud[1]['marca']) ? $productosSolicitud[1]['marca'] : '',
                    'pMod2' => isset($productosSolicitud[1]) && isset($productosSolicitud[1]['modelo']) ? $productosSolicitud[1]['modelo'] : '',
                    'pCol2' => isset($productosSolicitud[1]) && isset($productosSolicitud[1]['color']) ? $productosSolicitud[1]['color'] : '',
                    'pCod2' => isset($productosSolicitud[1]) && isset($productosSolicitud[1]['codigo_producto']) ? $productosSolicitud[1]['codigo_producto'] : '',
                    'pPre2' => isset($productosSolicitud[1]) && isset($productosSolicitud[1]['precio']) ? $productosSolicitud[1]['precio'] : '',

                    'pM3' => isset($productosSolicitud[2]) && isset($productosSolicitud[2]['marca']) ? $productosSolicitud[2]['marca'] : '',
                    'pMod3' => isset($productosSolicitud[2]) && isset($productosSolicitud[2]['modelo']) ? $productosSolicitud[2]['modelo'] : '',
                    'pCol3' => isset($productosSolicitud[2]) && isset($productosSolicitud[2]['color']) ? $productosSolicitud[2]['color'] : '',
                    'pCod3' => isset($productosSolicitud[2]) && isset($productosSolicitud[2]['codigo_producto']) ? $productosSolicitud[2]['codigo_producto'] : '',
                    'pPre3' => isset($productosSolicitud[2]) && isset($productosSolicitud[2]['precio']) ? $productosSolicitud[2]['precio'] : '',

                    'pM4' => isset($productosSolicitud[3]) && isset($productosSolicitud[3]['marca']) ? $productosSolicitud[3]['marca'] : '',
                    'pMod4' => isset($productosSolicitud[3]) && isset($productosSolicitud[3]['modelo']) ? $productosSolicitud[3]['modelo'] : '',
                    'pCol4' => isset($productosSolicitud[3]) && isset($productosSolicitud[3]['color']) ? $productosSolicitud[3]['color'] : '',
                    'pCod4' => isset($productosSolicitud[3]) && isset($productosSolicitud[3]['codigo_producto']) ? $productosSolicitud[3]['codigo_producto'] : '',
                    'pPre4' => isset($productosSolicitud[3]) && isset($productosSolicitud[3]['precio']) ? $productosSolicitud[3]['precio'] : '',

                    'pM5' => isset($productosSolicitud[4]) && isset($productosSolicitud[4]['marca']) ? $productosSolicitud[4]['marca'] : '',
                    'pMod5' => isset($productosSolicitud[4]) && isset($productosSolicitud[4]['modelo']) ? $productosSolicitud[4]['modelo'] : '',
                    'pCol5' => isset($productosSolicitud[4]) && isset($productosSolicitud[4]['color']) ? $productosSolicitud[4]['color'] : '',
                    'pCod5' => isset($productosSolicitud[4]) && isset($productosSolicitud[4]['codigo_producto']) ? $productosSolicitud[4]['codigo_producto'] : '',
                    'pPre5' => isset($productosSolicitud[4]) && isset($productosSolicitud[4]['precio']) ? $productosSolicitud[4]['precio'] : '',

                    'valorTotalArticulos' => isset($planPago[0]['valor_articulo']) ? $planPago[0]['valor_articulo'] : '',
                    'pagoPrima' => isset($planPago[0]['valor_prima']) ? $planPago[0]['valor_prima'] : '',
                    'saldoAPagar' => isset($planPago[0]['saldo_a_pagar']) ? $planPago[0]['saldo_a_pagar'] : '',
                    'nCuotas' => isset($planPago[0]['cuotas']) ? $planPago[0]['cuotas'] : '',
                    'vCuotas' => isset($planPago[0]['monto_cuotas']) ? $planPago[0]['monto_cuotas'] : '',
                    'observacionPP' => isset($planPago[0]['observaciones']) ? $planPago[0]['observaciones'] : '',
                    'montoTotal' => isset($solicitudEncontrada['monto_solicitud']) ? $solicitudEncontrada['monto_solicitud'] : '',

                    'nombreCodeudor' => isset($codeudor[0]['nombre']) ? $codeudor[0]['nombre'] : '',
                    'duiCodeudor' => isset($codeudor[0]['dui']) ? $codeudor[0]['dui'] : '',
                    'direccionCodeudor' => isset($codeudor[0]['direccion']) ? $codeudor[0]['direccion'] : '',
                    'telCode' => isset($codeudor[0]['telefono_personal']) ? $codeudor[0]['telefono_personal'] : '',
                    'cPCode' => isset($codeudor[0]['vive_en_casa_propia']) ? ($codeudor[0]['vive_en_casa_propia'] === 'si' ? 'Sí' : 'No') : '',
                    'pVCode' => isset($codeudor[0]['en_promesa_de_venta']) ? ($codeudor[0]['en_promesa_de_venta'] === 'si' ? 'Sí' : 'No') : '',
                    'alqCode' => isset($codeudor[0]['alquilada']) ? ($codeudor[0]['alquilada'] === 'si' ? 'Sí' : 'No') : '',
                    'tVivirCode' => isset($codeudor[0]['tiempo']) ? $codeudor[0]['tiempo'] : '',
                    'estadoCivilCode' => isset($codeudor[0]['estado_civil']) ? $codeudor[0]['estado_civil'] : '',
                    'nombreConyugueCode' => isset($codeudor[0]['nombre_conyugue']) ? $codeudor[0]['nombre_conyugue'] : '',
                    'profesionCode' => isset($codeudor[0]['profesion_oficio']) ? $codeudor[0]['profesion_oficio'] : '',
                    'lugarTrabajoCode' => isset($codeudor[0]['patrono_empresa']) ? $codeudor[0]['patrono_empresa'] : '',
                    'direccionTrabajoCode' => isset($codeudor[0]['direccion_trabajo']) ? $codeudor[0]['direccion_trabajo'] : '',
                    'telTrabCode' => isset($codeudor[0]['telefono_trabajo']) ? $codeudor[0]['telefono_trabajo'] : '',
                    'cargoCode' => isset($codeudor[0]['cargo']) ? $codeudor[0]['cargo'] : '',
                    'salarioCode' => isset($codeudor[0]['salario']) ? $codeudor[0]['salario'] : '',
                    'nombJefeCode' => isset($codeudor[0]['nombre_jefe_inmediato']) ? $codeudor[0]['nombre_jefe_inmediato'] : '',

                    'nomRefCode1' => isset($refCodeudor[0]['nombre']) ? $refCodeudor[0]['nombre'] : '',
                    'parentescoCode1' => isset($refCodeudor[0]['parentesco']) ? $refCodeudor[0]['parentesco'] : '',
                    'direccionRefCode1' => isset($refCodeudor[0]['direccion']) ? $refCodeudor[0]['direccion'] : '',
                    'telRefCode1' => isset($refCodeudor[0]['telefono']) ? $refCodeudor[0]['telefono'] : '',

                    'nomRefCode2' => isset($refCodeudor[1]['nombre']) ? $refCodeudor[1]['nombre'] : '',
                    'parentescoCode2' => isset($refCodeudor[1]['parentesco']) ? $refCodeudor[1]['parentesco'] : '',
                    'direccionRefCode2' => isset($refCodeudor[1]['direccion']) ? $refCodeudor[1]['direccion'] : '',
                    'telRefCode2' => isset($refCodeudor[1]['telefono']) ? $refCodeudor[1]['telefono'] : '',

                    'nomRefCode3' => isset($refCodeudor[2]['nombre']) ? $refCodeudor[2]['nombre'] : '',
                    'parentescoCode3' => isset($refCodeudor[2]['parentesco']) ? $refCodeudor[2]['parentesco'] : '',
                    'direccionRefCode3' => isset($refCodeudor[2]['direccion']) ? $refCodeudor[2]['direccion'] : '',
                    'telRefCode3' => isset($refCodeudor[2]['telefono']) ? $refCodeudor[2]['telefono'] : ''
                ];

                $numeroSolicitud = $solicitudEncontrada['numero_solicitud'];
                $rutaCarpeta = FCPATH . 'public/documentos/' . $numeroSolicitud . '/';
                if (!is_dir($rutaCarpeta)) {
                    mkdir($rutaCarpeta, 0755, true);
                }
                $rutaArchivo = $rutaCarpeta . 'solicitud_credito_' . $numeroSolicitud . '.docx';

                foreach ($datos as $clave => $valor) {
                    $templateProcessor->setValue("{{$clave}}", $valor);
                }

                $templateProcessor->saveAs($rutaArchivo);
                return $this->response->download($rutaArchivo, null)->setFileName('solicitud_credito_' . $numeroSolicitud . '.docx');
            } else {
                return $this->response->setStatusCode(400)->setBody("Solicitud no válida.");
            }
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setBody('Error al generar el documento: ' . $e->getMessage());
        }
    }

    public function generarContrato($id_solicitud)
    {
        log_message("info", "***************************************generarContrato***************************************");
        $session = session();
        log_message("info", "Valor que entra: ".$id_solicitud);
        try {
            if ($id_solicitud) {
                $templatePath = FCPATH . 'public/documentos/contrato/contratoV1.docx';

                if (!file_exists($templatePath)) {
                    throw new \Exception('El archivo de plantilla no se encuentra en la ruta especificada.');
                }

                $templateProcessor = new TemplateProcessor($templatePath);

                // Obtener los datos de la solicitud y cliente
                $solicitudEncontrada = $this->solicitudesModel->find($id_solicitud);
                log_message("info", "Valor que entra: ".print_r($solicitudEncontrada));
                $clienteEncontrado = $this->clientesModel->buscarCliente(null, (int) $solicitudEncontrada['id_cliente']);

                //obtener apoderado y reprelegal
                $datosLegales = $this->apoderadoModel->getApoderados();

                //productos solicitados
                $productosSolicitud = $this->prodSolicitudModel->buscarPorSolicitud($id_solicitud);
                $resultadoProdLista = $this->formatearProductos($productosSolicitud);

                //datos del plan de pago
                $planPago = $this->planDePagoModel->buscarPorSolicitud($id_solicitud);

                $codeudor = $this->codeudorModel->buscarPorSolicitud($id_solicitud);

                // Crear carpeta de destino si no existe
                $numeroSolicitud = $solicitudEncontrada['numero_solicitud'];
                $rutaCarpeta = FCPATH . 'public/documentos/' . $numeroSolicitud . '/';
                if (!is_dir($rutaCarpeta)) {
                    mkdir($rutaCarpeta, 0755, true);
                }

                $rutaArchivo = $rutaCarpeta . 'contrato_' . $numeroSolicitud . '.docx';
                $numero_contrato = $this->generarNumeroContrato($solicitudEncontrada['id_sucursal']);
                log_message("info", "numero de contrato generado:: ::: " . $numero_contrato);

                $meses = ['ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE'];

                // Datos a reemplazar en la plantilla
                $datos = [
                    'representanteLegal' => $datosLegales[0]['representante_legal'],
                    'edadRepresentanteLet' => $this->convertidoresController->calcularEdadEnLetras($datosLegales[0]['fecha_nacimiento_rLegal']),
                    'duiRepresentante' => $datosLegales[0]['dui_representante'],
                    'mueblesSolicitados' => $resultadoProdLista,
                    'sucursal' => $_SESSION['sucursalN'],
                    'cantidadCuotas' => $planPago[0]['cuotas'],
                    'precioTotal' => '$'.$planPago[0]['monto_total_pagar'],
                    'valorPrima' => '$'.$planPago[0]['valor_prima'],
                    'montoCuotas' => '$'.$planPago[0]['monto_cuotas'],
                    'diaLetra' => 'veinticinco',
                    'mesLetra' => 'septiembre',
                    'anioLetra' => 'veinticuatro',
                    'montoTotalPagar' => '$'.$planPago[0]['monto_total_pagar'],
                    'inicioPeriodoPago' => $this->convertidoresController->convertirFechaATexto($solicitudEncontrada['fecha_creacion']),
                    'diasPago' => (new \DateTime($solicitudEncontrada['fecha_creacion']))->format('j'),
                    'nombreFiador' => isset($codeudor[0]['nombre']) ? $codeudor[0]['nombre'] : '',
                    'domicilioFiador' => isset($codeudor[0]['direccion']) ? $codeudor[0]['direccion'] : '',
                    'duiFiador' => isset($codeudor[0]['dui']) ? $codeudor[0]['dui'] : '',
                    'nitFiador' => isset($codeudor[0]['dui']) ? $codeudor[0]['dui'] : '',
                    'nombreCliente' => !empty($clienteEncontrado['nombre_completo']) ? $clienteEncontrado['nombre_completo'] : '',
                    'ciudadContrato' => 'San Salvador',//falta definir
                    'ciudadSucursal' => 'San Salvador',//falta definir
                    'horaDefault' => '10:00 AM',
                    'diaCreacionSol' => (new \DateTime($solicitudEncontrada['fecha_creacion']))->format('j'),
                    'mesCreacionSol' => $meses[(new \DateTime($solicitudEncontrada['fecha_creacion']))->format('n') - 1],
                    'anioCreacionSol' => (new \DateTime($solicitudEncontrada['fecha_creacion']))->format('Y'),
                    'nombreApoderado' => $datosLegales[0]['nombre_apoderado'],
                    'domicilioApoderado' => 'Calle Central, San Salvador',
                    'duiCliente' => !empty($clienteEncontrado['dui']) ? $clienteEncontrado['dui'] : ''
                ];

                foreach ($datos as $clave => $valor) {
                    $valorMayusculas = mb_strtoupper($valor, 'UTF-8'); // Convertir a mayúsculas respetando la codificación
                    log_message("info", "Reemplazando {$clave} con {$valorMayusculas}");
                    $templateProcessor->setValue(trim("{{$clave}}"), $valorMayusculas);
                }

                // Guardar el archivo de contrato generado
                $templateProcessor->saveAs($rutaArchivo);

                // Insertar los datos del contrato en la base de datos
                $datosInsert = [
                    'dir_contrato' => $rutaArchivo,
                    'id_sucursal' => $solicitudEncontrada['id_sucursal'],
                    'num_contrato' => $numero_contrato,
                    'numero_solicitud' => $numeroSolicitud
                ];
                $this->contratoSolicitud->insert($datosInsert);

                return [
                    'success' => true,
                    'message' => 'Contrato generado y guardado exitosamente',
                    'solicitud' => $numeroSolicitud
                ];
            }

            log_message("info", "***************************************FIN generarContrato***************************************");
        } catch (\Throwable $e) {
            $errorMessage = 'Ocurrió un error: ' . $e->getMessage() . PHP_EOL;
            $errorMessage .= 'Trace: ' . $e->getTraceAsString();

            log_message('info','GenerarSolicitudCreditoController ------> generarContrato::: '. print_r($errorMessage,true));

            return [
                'success' => false,
                'message' => 'Error al generar el contrato'
            ];
        }
    }

    public function validarContrato($solicitud){
        try {
            $existeContrato = $this->contratoSolicitud->existeContratoPorSolicitud($solicitud);
            if ($existeContrato) {
                return [
                    'success' => true,
                    'message' => 'existe',
                    'solicitud' => $solicitud
                ];
            }else {
                return [
                    'success' => false,
                    'message' => 'no_existe',
                    'solicitud' => $solicitud
                ];
            }
        } catch (\Throwable $e) {
            $errorMessage = 'Ocurrió un error: ' . $e->getMessage() . PHP_EOL;
            $errorMessage .= 'Trace: ' . $e->getTraceAsString();
        }
    }

    public function formatearProductos($productosSolicitud) {
        $productos = [];
    
        foreach ($productosSolicitud as $producto) {
            $productos[] = $producto['nombre']; // Asumiendo que el nombre del producto está en el campo 'nombre'
        }
    
        // Formatear la cadena
        if (count($productos) > 1) {
            $ultimo = array_pop($productos); // Extraer el último elemento
            $resultado = implode(', ', $productos) . ' y ' . $ultimo; // Unir con comas y agregar "y"
        } elseif (count($productos) === 1) {
            $resultado = $productos[0]; // Si solo hay un producto
        } else {
            $resultado = ''; // Si no hay productos
        }
    
        return $resultado;
    }

    public function generarNumeroContrato($id_sucursal)
    {
        $ultimo_num_contrato = 0;
        $codigo_sucursal = '';
        $nuevo_num_contrato = '';

        // Obtener el código de la sucursal
        $resultado = $this->contratoModel->getCodigoSucursal($id_sucursal);
        if ($resultado) {
            $codigo_sucursal = $resultado->codigo_sucursal;
        } else {
            throw new \Exception('Código de sucursal no encontrado');
        }

        // Obtener el último número de contrato de la sucursal
        $resultado = $this->contratoModel->getUltimoNumeroContrato($id_sucursal);
        if ($resultado) {
            $ultimo_num_contrato = $resultado->ultimo_num_contrato;
        }

        // Incrementar el número de contrato
        $ultimo_num_contrato++;

        // Formatear el nuevo número de contrato
        $nuevo_num_contrato = $codigo_sucursal . '-' . str_pad($ultimo_num_contrato, 8, '0', STR_PAD_LEFT);

        // Insertar mensaje en la tabla de log
        $this->contratoModel->insertarLog('Código de sucursal recuperado: ' . $codigo_sucursal);
        $this->contratoModel->insertarLog('Nuevo número de contrato generado: ' . $nuevo_num_contrato);

        // Devolver el nuevo número de contrato
        return $nuevo_num_contrato;
    }

    public function descargar($solicitud)
    {
        log_message("info", "llega la sol::: " . $solicitud);
        $rspRuta = $this->contratoSolicitud->where('numero_solicitud', $solicitud)->first();
        log_message("info", "respuesta del select::: " . $rspRuta["dir_contrato"]);
        $rutaArchivo = $rspRuta["dir_contrato"];

        // Verificar y construir la ruta completa
        $rutaCompleta =  $rutaArchivo;

        if (file_exists($rutaCompleta)) {
            return $this->response->download($rutaCompleta, null)->setContentType('application/octet-stream');
        } else {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Archivo no encontrado');
        }
    }
}
