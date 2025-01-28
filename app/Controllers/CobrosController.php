<?php

namespace App\Controllers;

use App\Models\SolicitudModel;
use App\Models\CobrosModel;
use PhpOffice\PhpWord\TemplateProcessor;
use NumberFormatter;

class CobrosController extends BaseController
{
    public function index()
    {
        $session = session();
        if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
            $content4 = view('cobros/cobros');
            $fullPage = $this->renderPage($content4);
            return $fullPage;
        } else {
            return redirect()->to(base_url());
        }
    }

    public function getCobrosClientes()
    {
        $session = session();
        if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
            $json = $this->request->getJSON();

            if ($json && isset($json->duiCliente)) {
                $duiCliente = $json->duiCliente;
            } else {
                $duiCliente = $this->request->getPost('duiCliente');
            }

            $modelSolicitud = new SolicitudModel();
            $modelCobros = new CobrosModel();
            $solicitudXUsuario = $modelSolicitud->solicitudAprobadasPorCliente($duiCliente);

            $result = [];

            foreach ($solicitudXUsuario as $data) {
                $result[] = $data;
            }
            log_message('info', 'Resultado JSON: ' . json_encode($result));
            return $this->response->setJSON($result);
        } else {
            return redirect()->to(base_url());
        }
    }

    public function getDeudasPorSolicitud()
    {
        $session = session();
        if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
            $json = $this->request->getJSON();

            $id_solicitud = $json->id_solicitud;

            log_message('info', 'Resultado JSON id_solicitud: ' . $id_solicitud);
            $modelCobros = new CobrosModel();
            $deudasSolicitud = $modelCobros->getCobrosBySolicitud($id_solicitud);
            log_message('info', 'Resultado JSON deudasSolicitud: ' . print_r($deudasSolicitud, true));
            $result = [];

            foreach ($deudasSolicitud as $data) {
                $result[] = $data;
            }
            return $this->response->setJSON($result);
        } else {
            return redirect()->to(base_url());
        }
    }

    public function procesarPagos()
    {
        try {
            $inputData = $this->request->getJSON(true);
            $solicitudModel = new SolicitudModel();
            $modelCobros = new CobrosModel();

            // Iniciar transacción
            $db = \Config\Database::connect();
            $db->transStart();

            if (!empty($inputData['montoTotalaCancelar']) && !empty($inputData['solicitud'])) {
                //$arrayPagos = $inputData['pagos'];
                $solicitudNu = $inputData['solicitud'];
                $cuotasCubiertas = $inputData['cuotasCubiertas'];
                $moraTotalAPagar = $inputData['moraTotalAPagar'];
                $saldoRestante = $inputData['saldoRestante'];
                $cuotasActualizadas = 0; // Contador para cuotas cubiertas
                $sumaCuotas = 0.0;
                $var = 1;
                $cobrosCancelados = [];
                $recalculoMontoPagar = 0;

                $datosCobros = $modelCobros->getCobrosPendientesByNumeroSolicitud($solicitudNu);
                foreach ($datosCobros as $index => $cobro) {
                    // Verificar si quedan cuotas por cubrir
                    if ($cuotasActualizadas < $cuotasCubiertas) {
                        // Actualizar mora para el primer registro
                        if ($index === 0 && $moraTotalAPagar > 0) {
                            $descripcion = "Pago de la cuota " . $var . " con valor de cuota de $" . $cobro->monto_cuota;
                            if (isset($moraTotalAPagar) && $moraTotalAPagar > 0) {
                                $descripcion .= ", más un interés generado de $" . $moraTotalAPagar;
                            }
                            $modelCobros->update($cobro->id_cobro, [
                                'estado' => 'CANCELADO',
                                'interesGenerado' => $moraTotalAPagar, // Se actualiza la mora solo para el primer registro
                                'fecha_pago' => date('Y-m-d H:i:s'),
                                'descripcion' => $descripcion,
                                'cantAbono' => $cobro->monto_cuota
                            ]);
                            $recalculoMontoPagar += $cobro->monto_cuota;
                            $cobrosCancelados[] = $cobro;
                            log_message('info', "Cobro ID {$cobro->id_cobro} actualizado con mora de {$moraTotalAPagar} y estado CANCELADO.");
                        } else {
                            // Actualizar solo el estado para los demás registros
                            $descripcion = "Pago de la cuota " . $var . " con valor de cuota de $" . $cobro->monto_cuota;
                            $modelCobros->update($cobro->id_cobro, [
                                'estado' => 'CANCELADO',
                                'fecha_pago' => date('Y-m-d H:i:s'),
                                'descripcion' => $descripcion,
                                'cantAbono' => $cobro->monto_cuota
                            ]);
                            $recalculoMontoPagar += $cobro->monto_cuota;
                            $cobrosCancelados[] = $cobro;
                            log_message('info', "Cobro ID {$cobro->id_cobro} actualizado a CANCELADO.");
                        }

                        $cuotasActualizadas++; // Incrementar el contador de cuotas actualizadas
                        $var++;
                    } else if (isset($saldoRestante) && $saldoRestante > 0) {
                        $descripcion = "Abono de la cuota con valor de abono de $" . $saldoRestante;
                        $modelCobros->update($cobro->id_cobro, [
                            'estado' => 'PENDIENTE',
                            'fecha_pago' => date('Y-m-d H:i:s'),
                            'descripcion' => $descripcion,
                            'cantAbono' => $saldoRestante
                        ]);
                        $cobrosCancelados[] = $cobro;
                        $recalculoMontoPagar += $saldoRestante;
                        $saldoRestante = $saldoRestante - $saldoRestante;
                    }
                }
                /* foreach ($arrayPagos as $pago) {
                    // Generar descripción del pago
                    $descripcion = "Pago de la cuota " . $pago['numero_cuota'] . " con valor de cuota de $" . $pago['montoCuota'];
                    if (isset($pago['mora']) && $pago['mora'] > 0) {
                        $descripcion .= ", más un interés generado de $" . $pago['mora'];
                    }

                    // Actualizar el registro en la tabla "cobros"
                    $modelCobros->update($pago['id'], [
                        'estado' => 'CANCELADO',
                        'interesGenerado' => $pago['mora'],
                        'descripcion' => $descripcion,
                        'fecha_pago' => date('Y-m-d H:i:s'),
                    ]);

                    log_message('info', 'Cobro actualizado: ' . json_encode($pago));
                    $var++;
                    $sumaCuotas = bcadd($sumaCuotas, $pago['montoCuota'], 2);
                } */

                $solicitud = $solicitudModel->getSolicitud($solicitudNu);
                if (!$solicitud) {
                    throw new \Exception('La solicitud no fue encontrada.');
                }

                $montoApagar = (float) $solicitud['montoApagar'];
                $idSolicitud = $solicitud['id_solicitud'];

                $nuevoMontoApagar = $montoApagar - $recalculoMontoPagar;
                if ($nuevoMontoApagar < 0) {
                    throw new \Exception('El monto a pagar no puede ser negativo.');
                }

                $solicitudModel->update($idSolicitud, ['montoApagar' => $nuevoMontoApagar]);

                $db->transComplete();

                if ($db->transStatus() === false) {
                    throw new \Exception('Ocurrió un error al confirmar la transacción.');
                }

                $rutaGenerada = $this->generarDocPagos($cobrosCancelados);
                $esValidaRuta = !empty($rutaGenerada) ? true : false;

                return $this->response->setJSON([
                    'ok' => true,
                    'message' => 'Los pagos se procesaron correctamente.',
                    'documento' => $rutaGenerada,
                    'flagDoc' => $esValidaRuta
                ]);
            } else {
                return $this->response->setJSON([
                    'ok' => false,
                    'message' => 'No se recibieron datos para procesar.',
                ]);
            }
        } catch (\Throwable $th) {
            // Realizar rollback en caso de error
            $db->transRollback();
            log_message('error', 'Error al procesar los pagos: ' . $th->getMessage());
            return $this->response->setJSON([
                'ok' => false,
                'message' => 'Ocurrió un error al procesar los pagos. Por favor, inténtelo de nuevo.',
                'error' => $th->getMessage(),
            ]);
        }
    }


    public function generarDocPagos($arrayPagos)
    {
        log_message("info", "***************************************generarDocPagos***************************************");
        $session = session();
        try {
            $modelCobros = new CobrosModel();
            $solicitudesModel = new SolicitudModel();

            if (!$arrayPagos) {
                throw new \Exception("El array de pagos está vacío.");
            }

            $templatePath = FCPATH . 'public/documentos/pagos/Formato_Factura.docx';

            if (!file_exists($templatePath)) {
                throw new \Exception('El archivo de plantilla no se encuentra en la ruta especificada.');
            }

            $templateProcessor = new TemplateProcessor($templatePath);

            $cobrosCancelados = [];
            $numCuot = '';
            foreach ($arrayPagos as $pago) {
                // Buscar el cobro por id
                $cobro = $modelCobros->getCobroById($pago->id_cobro);

                // Verificar si el cobro está cancelado o parcialmente pagado
                log_message('info', 'generarDocPagos ------> Cobros: ' . print_r($cobro, true));
                if (($cobro && $cobro['estado'] == 'CANCELADO') || ($cobro['cantAbono'] < $cobro['monto_cuota'] && $cobro['cantAbono'] > 0)) {
                    $cobrosCancelados[] = $cobro;
                    $numCuot .= ($numCuot !== '' ? '_' : '') . $cobro['numero_cuota'];
                }
            }

            if (count($cobrosCancelados) === 0) {
                throw new \Exception('No se encontraron cobros cancelados o con abonos.');
            }

            log_message('info', 'generarDocPagos ------> Cobros cancelados: ' . print_r($cobrosCancelados, true));

            // Buscar la solicitud asociada
            $solicitudEncontrada = $solicitudesModel->find($cobrosCancelados[0]['id_solicitud']);
            $numeroSolicitud = $solicitudEncontrada['numero_solicitud'];

            // Crear carpeta de destino si no existe
            $rutaCarpeta = FCPATH . 'public/documentos/pagos/' . $numeroSolicitud . '/';
            if (!is_dir($rutaCarpeta)) {
                mkdir($rutaCarpeta, 0755, true);
            }

            $rutaArchivo = $rutaCarpeta . 'pagoCuota' . $numeroSolicitud . '_' . $numCuot . '.docx';
            $rutaArchivoRetorno = 'public/documentos/pagos/' . $numeroSolicitud . '/pagoCuota' . $numeroSolicitud . '_' . $numCuot . '.docx';

            $modelSolicitud = new SolicitudModel();
            $datosCobros = $modelSolicitud->getDatosCobrosC($numeroSolicitud);
            $templateProcessor->setValue("nombreCliente", $datosCobros[0]['nombre_completo']);
            $templateProcessor->setValue("noContrato", $datosCobros[0]['num_contrato']);

            $totalMonto = 0;
            $nCuotasAlmacen = "";
            $descripcionAbono = "";
            $descripcionMora = "";
            $sumaCuotasPagadaYabono = 0.0;
            $valorInteres = 0.00;

            // Procesar los cobros cancelados o con abonos
            //Clonar filas en la tabla y reemplazar datos
            $templateProcessor->cloneRow('descripcion', 2);
            foreach ($cobrosCancelados as $pago) {
                if ($pago['estado'] === 'CANCELADO') {
                    $nCuotasAlmacen .= ($nCuotasAlmacen !== '' ? ', ' : '') . $pago['numero_cuota'];
                    $sumaCuotasPagadaYabono += $pago['cantAbono'];
                } elseif ($pago['estado'] === 'PENDIENTE' && $pago['cantAbono'] < $pago['monto_cuota']) {
                    $descripcionAbono .= " Abono a cuota número " . $pago['numero_cuota'].', la cantidad de '.$pago['cantAbono'];
                    $sumaCuotasPagadaYabono += $pago['cantAbono'];
                }

                if ($pago['estado'] === 'CANCELADO' && $pago['interesGenerado'] > 0) {
                    $descripcionMora = "Cobro de interés por mora de $" . number_format($pago['interesGenerado'], 2);
                    $totalMonto += $pago['interesGenerado'];
                    $valorInteres = $pago['interesGenerado'];
                }
            }

            // Construir descripción de las cuotas
            $descripcionNew = "";
            if (!empty($nCuotasAlmacen)) {
                $descripcionNew = "Pago de cuota(s) número(s) " . $nCuotasAlmacen;
            }
            if (!empty($descripcionAbono)) {
                $descripcionNew .=!empty($nCuotasAlmacen) ? ", Y ".$descripcionAbono : $descripcionAbono;
            }

            // Configurar valores en el documento
            $templateProcessor->setValue("descripcion#1", ucfirst(mb_strtolower($descripcionNew, 'UTF-8')));
            $templateProcessor->setValue("cant#1", '1');
            $templateProcessor->setValue("pUni#1", '$' . number_format($sumaCuotasPagadaYabono, 2));
            $templateProcessor->setValue("totalU#1", '$' . number_format($sumaCuotasPagadaYabono, 2));
            $totalMonto += $sumaCuotasPagadaYabono;

            if (!empty($descripcionMora)) {
                $templateProcessor->setValue("descripcion#2", ucfirst(mb_strtolower($descripcionMora, 'UTF-8')));
                $templateProcessor->setValue("cant#2", '1');
                $templateProcessor->setValue("pUni#2", '$' . number_format($valorInteres, 2));
                $templateProcessor->setValue("totalU#2", '$' . number_format($valorInteres, 2));
            }

            log_message("info", "valor del datosCobros::: " . print_r($datosCobros, true));

            $templateProcessor->setValue("sumaTotal", '$' . number_format($totalMonto, 2));
            $totalEnLetras = $this->convertirNumeroALetras($totalMonto);
            $templateProcessor->setValue("totalApagarLetras", $totalEnLetras);

            // Guardar el archivo generado
            $templateProcessor->saveAs($rutaArchivo);

            log_message("info", "Documento generado exitosamente: {$rutaArchivoRetorno}");

            return $rutaArchivoRetorno;
        } catch (\Throwable $e) {
            $errorMessage = 'Ocurrió un error: ' . $e->getMessage() . PHP_EOL;
            $errorMessage .= 'Trace: ' . $e->getTraceAsString();

            log_message('error', 'Error en generarDocPagos: ' . $errorMessage);

            return [
                'success' => false,
                'message' => 'Error al generar el documento de pagos'
            ];
        }
    }


    function convertirNumeroALetras($numero)
    {
        $formatter = new NumberFormatter('es', NumberFormatter::SPELLOUT);

        // Obtener la parte entera y los decimales
        $parteEntera = floor($numero);
        $centavos = round(($numero - $parteEntera) * 100);

        // Convertir la parte entera a letras
        $letrasParteEntera = mb_strtoupper($formatter->format($parteEntera), 'UTF-8');

        // Construir el resultado
        $resultado = "{$letrasParteEntera} DÓLARES";

        // Agregar los centavos si existen
        if ($centavos > 0) {
            $resultado .= " CON {$formatter->format($centavos)} CENTAVOS";
        }

        return strtoupper($resultado);
    }


    public function descargarDocumentoCobros()
    {
        $ruta = $this->request->getPost('ruta');
        log_message("info", "llega la ruta::: " . $ruta);

        if (file_exists($ruta)) {
            return $this->response->download($ruta, null)
                ->setFileName(basename($ruta))
                ->setContentType('application/octet-stream');
        } else {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Archivo no encontrado');
        }
    }
}
