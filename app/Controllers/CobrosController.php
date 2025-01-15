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

            if (!empty($inputData['pagos']) && !empty($inputData['solicitud'])) {
                $arrayPagos = $inputData['pagos'];
                $solicitudNu = $inputData['solicitud'];

                $sumaCuotas = 0.0;
                $var = 1;

                foreach ($arrayPagos as $pago) {
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
                }

                $solicitud = $solicitudModel->getSolicitud($solicitudNu);
                if (!$solicitud) {
                    throw new \Exception('La solicitud no fue encontrada.');
                }

                $montoApagar = (float) $solicitud['montoApagar'];
                $idSolicitud = $solicitud['id_solicitud'];

                $nuevoMontoApagar = $montoApagar - $sumaCuotas;
                if ($nuevoMontoApagar < 0) {
                    throw new \Exception('El monto a pagar no puede ser negativo.');
                }

                $solicitudModel->update($idSolicitud, ['montoApagar' => $nuevoMontoApagar]);

                $db->transComplete();

                if ($db->transStatus() === false) {
                    throw new \Exception('Ocurrió un error al confirmar la transacción.');
                }

                $rutaGenerada = $this->generarDocPagos($arrayPagos);
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
            if ($arrayPagos) {
                $templatePath = FCPATH . 'public/documentos/pagos/Formato_Factura.docx';

                if (!file_exists($templatePath)) {
                    throw new \Exception('El archivo de plantilla no se encuentra en la ruta especificada.');
                }

                $templateProcessor = new TemplateProcessor($templatePath);

                $cobrosCancelados = [];
                $numCuot = '';
                foreach ($arrayPagos as $pago) {
                    // Buscar el cobro por id
                    $cobro = $modelCobros->getCobroById($pago['id']);

                    // Verificar si el cobro está cancelado
                    if ($cobro && $cobro['estado'] == 'CANCELADO') {
                        $cobrosCancelados[] = $cobro;
                        if ($numCuot !== '') {
                            $numCuot .= '_';
                        }
                        $numCuot .= $cobro['numero_cuota'];
                    }
                }

                if (count($cobrosCancelados) > 0) {
                    log_message('info', 'generarDocPagos ------> Cobros cancelados: ' . print_r($cobrosCancelados, true));
                }

                // Buscar la solicitud
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
                // Clonar filas en la tabla y reemplazar datos
                $templateProcessor->cloneRow('descripcion', count($cobrosCancelados));
                foreach ($cobrosCancelados as $index => $pago) {
                    $fila = $index + 1; // Las filas en la plantilla inician desde 1
                    /* $templateProcessor->setValue("descripcion#{$fila}", mb_strtoupper($pago['descripcion'], 'UTF-8')); */
                    $templateProcessor->setValue("descripcion#{$fila}", ucfirst(mb_strtolower($pago['descripcion'], 'UTF-8')) . ', segun contrato de arrendamiento: '.$datosCobros[0]['num_contrato'].' - Vencimiento: ' . $pago['fecha_vencimiento']);
                    $templateProcessor->setValue("cant#{$fila}", '1');
                    $templateProcessor->setValue("pUni#{$fila}", '$' . number_format($pago['monto_cuota'], 2));
                    $templateProcessor->setValue("totalU#{$fila}", '$' . number_format($pago['monto_cuota'], 2));

                    $totalMonto += $pago['monto_cuota'];
                }
                $templateProcessor->setValue("sumaTotal", '$' . number_format($totalMonto, 2));

                log_message("info", "valor del datosCobros::: ".print_r($datosCobros, true));

                $totalEnLetras = $this->convertirNumeroALetras($totalMonto);
                $templateProcessor->setValue("totalApagarLetras", $totalEnLetras);

                // Guardar el archivo generado
                $templateProcessor->saveAs($rutaArchivo);

                log_message("info", "Documento generado exitosamente: {$rutaArchivoRetorno}");
            }

            log_message("info", "***************************************FIN generarContrato***************************************");

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
