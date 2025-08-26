<?php

namespace App\Controllers;

use App\Models\SolicitudModel;
use App\Models\CobrosModel;
use App\Models\FacturasModel;
use App\Models\HistorialCobrosModel;
use PhpOffice\PhpWord\TemplateProcessor;
use App\Models\ProductoSolicitudModel;
use NumberFormatter;

class CobrosController extends BaseController
{

    private $prodSolicitudModel;
    public function __construct()
    {
        $this->prodSolicitudModel = new ProductoSolicitudModel();
    }
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

            $db = \Config\Database::connect();
            $result = [];

            foreach ($solicitudXUsuario as &$solicitud) {
                $codigoProductosVacio = empty($solicitud['codigos_productos']);
                $esSolicitudV = str_starts_with($solicitud['numero_solicitud'], 'V-');

                $productosDescripcion = ''; // Inicializamos el campo para descripción

                if ($codigoProductosVacio && $esSolicitudV) {
                    $productosAnteriores = $db->table('productos_solicitud_anterior')
                        ->select('articulo_principal, articulos_varios')
                        ->where('id_solicitud', $solicitud['id_solicitud'])
                        ->get()
                        ->getResultArray();

                    log_message('info', 'Entro a buscar otros productos JSON: ' . json_encode($productosAnteriores));

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
                        // Guardamos los códigos en codigos_productos como string
                        $solicitud['codigos_productos'] = implode(', ', $codigos);

                        // Buscar nombre de los productos
                        $detallesProductos = $db->table('productos')
                            ->select('codigo_producto, nombre')
                            ->whereIn('codigo_producto', $codigos)
                            ->get()
                            ->getResultArray();

                        $descripciones = [];
                        foreach ($detallesProductos as $producto) {
                            $descripciones[] = "{$producto['codigo_producto']} - {$producto['nombre']}";
                        }

                        $productosDescripcion = implode(', ', $descripciones);
                    }
                }

                // Agregamos el nuevo campo productos_descripcion al resultado
                $solicitud['productos_descripcion'] = $productosDescripcion;

                $result[] = $solicitud;
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
        $db = \Config\Database::connect();

        try {
            $session = session();
            $inputData = $this->request->getJSON(true);

            if (empty($inputData['montoTotalaCancelar']) || empty($inputData['solicitud'])) {
                return $this->response->setJSON(['ok' => false, 'message' => 'No se recibieron datos para procesar.']);
            }

            $solicitudNu = $inputData['solicitud'];;
            $montoApagar = $inputData['montoTotalaCancelar'] ?? 0;
            $arrayPago = $inputData['ArrayPago'];

            log_message("info", "Contenido de ArrayPago: " . print_r($arrayPago, true));

            $solicitudModel = new SolicitudModel();
            $modelCobros = new CobrosModel();
            $facturasModel = new FacturasModel();
            $modelHistorialCobro = new HistorialCobrosModel();

            $db->transStart(); // ✅ Iniciar transacción

            $idFactCreada = $facturasModel->insertarFactura($_SESSION['sucursal'], $_SESSION['id_usuario']);
            log_message('debug', "Valor retornado por insertarFactura: " . print_r($idFactCreada, true));
            if (!$idFactCreada['success']) {
                // Si hubo un error, detener el proceso y devolver la respuesta
                return $this->response->setJSON([
                    'ok' => false,
                    'message' => $idFactCreada['message']
                ]);
            }

            // Si todo salió bien, extraemos el ID de la factura
            $idFactura = $idFactCreada['id_factura'];
            $datosCobros = $modelCobros->getCobrosPendientesByNumeroSolicitud($solicitudNu);

            $mora = 0;
            $primeraCuota = true;

            foreach ($datosCobros as $cobro) {
                foreach ($arrayPago as $pagos) {
                    if ($pagos['id_cobro'] === 0 && $pagos['monto_abonado'] > 0) { // Es mora
                        $mora = $pagos['monto_abonado'];
                    }

                    if ($cobro->id_cobro === $pagos['id_cobro']) {
                        // Si es igual a uno es abono, toca validar el saldo 
                        $descripcionPago = "";
                        if ($pagos['completo'] === 1) {
                            $flagCompleta = ($pagos['monto_abonado'] < $cobro->monto_cuota) ? false : true;
                            if ($flagCompleta) {
                                # Es Completo
                                $descripcionPago = "Pago de la cuota " . ($cobro->numero_cuota) . " con valor de cuota de $" . $cobro->monto_cuota;
                                if ($mora > 0 && $primeraCuota) {
                                    $descripcionPago .= ", más un interés generado de $" . $mora;
                                }
                            } else {
                                # Es Abono para completar la cuota
                                $descripcionPago = "Pago de la cuota " . ($cobro->numero_cuota) . " con valor de $" . $pagos['monto_abonado'];
                            }

                            # Se suma el abono que se ingresa mas el abono que esta en la base, da el nuevo abono
                            $sumaAbono = $pagos['monto_abonado'] + $cobro->cantAbono;
                            # Se agrega el la cuota completa
                            $updateResult = $modelCobros->update($cobro->id_cobro, [
                                'estado' => 'CANCELADO',
                                'interesGenerado' => $mora,
                                'fecha_pago' => date('Y-m-d H:i:s'),
                                'descripcion' => $descripcionPago,
                                'cantAbono' => sprintf('%.2f', $sumaAbono)
                            ]);

                            if ($updateResult) {
                                $modelHistorialCobro->insert([
                                    'abono' => sprintf('%.2f', $pagos['monto_abonado'] + ($primeraCuota ? $mora : 0)),
                                    'id_cobro' => $cobro->id_cobro,
                                    'descripcion' => $descripcionPago,
                                    'id_factura' => $idFactura
                                ]);
                            }

                            $primeraCuota = false;
                            $mora = 0;
                        } else if ($pagos['completo'] === 0) { // Es abono
                            $descripcionAbono = "Abono de la cuota " . ($cobro->numero_cuota) . " con valor de abono de $" . $pagos['monto_abonado'];
                            if ($mora > 0 && $primeraCuota) {
                                $descripcionAbono .= ", más un interés generado de $" . $mora;
                            }
                            $sumaAbono = $pagos['monto_abonado'] + $cobro->cantAbono;
                            $updateResult = $modelCobros->update($cobro->id_cobro, [
                                'estado' => 'PENDIENTE',
                                'interesGenerado' => $mora,
                                'fecha_pago' => date('Y-m-d H:i:s'),
                                'descripcion' => $descripcionAbono,
                                'cantAbono' => sprintf('%.2f', $sumaAbono)
                            ]);

                            if (!$updateResult) {
                                throw new \Exception('Error al actualizar cobros.', 1002);
                            }

                            if ($updateResult) {
                                $modelHistorialCobro->insert([
                                    'abono' => sprintf('%.2f', $pagos['monto_abonado'] + ($primeraCuota ? $mora : 0)),
                                    'id_cobro' => $cobro->id_cobro,
                                    'descripcion' => $descripcionAbono,
                                    'id_factura' => $idFactura
                                ]);
                            }
                            $primeraCuota = false;
                            $mora = 0;
                        }
                    }
                }
            }

            // ✅ Obtener solicitud
            $solicitud = $solicitudModel->getSolicitud($solicitudNu);
            if (!$solicitud) {
                throw new \Exception('La solicitud no fue encontrada.', 1001);
            }

            // ✅ Recalcular el monto a pagar
            log_message("info", "Monto a pagar de la solicitud: " . $solicitud['montoApagar']);
            log_message("info", "Monto a pagar: " . $montoApagar);
            $nuevoMontoApagar = (float)$solicitud['montoApagar'] - (float)$montoApagar;
            if ($nuevoMontoApagar < 0) {
                throw new \Exception('El monto a pagar no puede ser negativo.');
            }

            // ✅ Actualizar la solicitud con el nuevo monto a pagar
            $solicitudModel->update($solicitud['id_solicitud'], ['montoApagar' => number_format($nuevoMontoApagar, 2, '.', '')]);

            $db->transComplete(); // ✅ Finalizar transacción

            if ($db->transStatus() === false) {
                throw new \Exception('Ocurrió un error al confirmar la transacción.');
            }

            // ✅ Generar documento de pagos
            $rutaGenerada = $this->generarDocPagos($idFactura, $solicitudNu);

            return $this->response->setJSON([
                'ok' => true,
                'message' => 'Los pagos se procesaron correctamente.',
                'documento' => $rutaGenerada,
                'flagDoc' => !empty($rutaGenerada)
            ]);
        } catch (\Throwable $th) {
            $db->transRollback();
            log_message('error', 'Error al procesar los pagos: ' . $th->getMessage());

            return $this->response->setJSON([
                'ok' => false,
                'message' => 'Ocurrió un error al procesar los pagos: ' . $th->getMessage(),
                'error_code' => $th->getCode()
            ]);
        }
    }

    public function generarDocPagos($idFactCreada, $solicitudNu)
    {
        log_message("info", "***************************************generarDocPagos***************************************");
        log_message("info", "el valor dsolicitudNu:: " . $solicitudNu);
        $session = session();
        try {
            $facturasModel = new FacturasModel();
            $modelSolicitud = new SolicitudModel();

            // Obtener historial de pagos de la factura
            $historialArray = $facturasModel->obtenerHistorialCobros($idFactCreada);
            if (!$historialArray) {
                log_message("info", "El historial de pagos está vacío.");
            }

            // Obtener datos del cliente y contrato
            $datosCobros = $modelSolicitud->getDatosCobrosC($solicitudNu);
            log_message("info", "valor del datosCobros:: " . print_r($datosCobros, true));
            if (empty($datosCobros)) {
                return $this->response->setJSON([
                    'ok' => false,
                    'message' => 'No se encontraron cobros pendientes para este registro.'
                ]);
            }

            $templatePath = FCPATH . 'public/documentos/pagos/Formato_Factura.docx';
            if (!file_exists($templatePath)) {
                throw new \Exception('El archivo de plantilla no se encuentra en la ruta especificada.');
            }

            $templateProcessor = new TemplateProcessor($templatePath);

            // Variables de acumulación
            $totalMonto = 0;
            $numCuot = '';
            $numeroFilas = count($historialArray);
            $templateProcessor->cloneRow('descripcion', $numeroFilas);
            foreach ($historialArray as $index => $pago) {
                $numCuot .= ($numCuot !== '' ? '_' : '') . ($index + 1);
                $templateProcessor->setValue("descripcion#" . ($index + 1), ucfirst(mb_strtolower($pago['descripcion'], 'UTF-8')));
                $templateProcessor->setValue("cant#" . ($index + 1), '1');
                $templateProcessor->setValue("pUni#" . ($index + 1), '$' . number_format($pago['abono'], 2));
                $templateProcessor->setValue("totalU#" . ($index + 1), '$' . number_format($pago['abono'], 2));
                $totalMonto += $pago['abono'];
            }

            // Total en letras
            $templateProcessor->setValue("sumaTotal", '$' . number_format($totalMonto, 2));
            $totalEnLetras = $this->convertirNumeroALetras($totalMonto);
            $templateProcessor->setValue("totalApagarLetras", $totalEnLetras);

            // Asignar datos del cliente y contrato
            $templateProcessor->setValue("nombreCliente", $datosCobros[0]['nombre_completo']);
            $templateProcessor->setValue("noContrato", $datosCobros[0]['no_factura']);


            // Crear carpeta de destino
            $rutaCarpeta = FCPATH . 'public/documentos/pagos/' . $solicitudNu . '/';
            if (!is_dir($rutaCarpeta)) {
                mkdir($rutaCarpeta, 0755, true);
            }

            /* $rutaArchivo = $rutaCarpeta . 'pagoCuota' . $idFactCreada . '_' . $numCuot . '.docx';
            $rutaArchivoRetorno = 'public/documentos/pagos/' . $idFactCreada . '/pagoCuota' . $idFactCreada . '_' . $numCuot . '.docx'; */
            $rutaArchivo = $rutaCarpeta . 'pagoCuota' . $solicitudNu . '_' . $numCuot . '.docx';
            $rutaArchivoRetorno = 'public/documentos/pagos/' . $solicitudNu . '/pagoCuota' . $solicitudNu . '_' . $numCuot . '.docx';
            $templateProcessor->saveAs($rutaArchivo);

            // Actualizar la ruta en la tabla factura
            $facturasModel->update($idFactCreada, ['ruta_factura' => $rutaArchivoRetorno]);

            log_message("info", "Documento generado exitosamente: {$rutaArchivoRetorno}");

            return $rutaArchivoRetorno;
        } catch (\Throwable $e) {
            log_message('error', 'Error en generarDocPagos: ' . $e->getMessage());
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

    public function estadoDeCuentas()
    {
        $session = session();
        if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
            $content4 = view('cobros/estadoDeCuentas');
            $fullPage = $this->renderPage($content4);
            return $fullPage;
        } else {
            return redirect()->to(base_url());
        }
    }


    public function procesarPagosContado($solicitud)
    {
        $db = \Config\Database::connect();

        try {
            $session = session();
            $solicitudNu = $solicitud;

            $solicitudModel = new SolicitudModel();
            $modelCobros = new CobrosModel();
            $facturasModel = new FacturasModel();
            $modelHistorialCobro = new HistorialCobrosModel();

            $db->transStart(); // Iniciar transacción

            // Generar la factura
            $idFactCreada = $facturasModel->insertarFactura($_SESSION['sucursal'], $_SESSION['id_usuario']);
            if (!$idFactCreada['success']) {
                return $this->response->setJSON([
                    'ok' => false,
                    'message' => $idFactCreada['message']
                ]);
            }

            $idFactura = $idFactCreada['id_factura'];

            // Obtener la solicitud
            $solicitud = $solicitudModel->getSolicitudXid($solicitudNu);
            if (!$solicitud) {
                throw new \Exception('La solicitud no fue encontrada.', 1001);
            }

            // Establecer el monto a pagar en 0 porque se canceló todo
            $solicitudModel->update($solicitud['id_solicitud'], ['montoApagar' => '0.00']);
            // Obtener los cobros pendientes
            $datosCobros = $modelCobros->getCobrosPendientesByIdSolicitud($solicitudNu);

            $productosXsoliContado = $productos = $this->prodSolicitudModel->buscarPorSolicitud($solicitud['id_solicitud']);


            foreach ($datosCobros as $cobro) {
                $descripcion = "Pago de contado " . $cobro->numero_cuota . " con valor de $" . $cobro->monto_cuota;
                // Marcar como cancelado
                $modelCobros->update($cobro->id_cobro, [
                    'estado' => 'CANCELADO',
                    'interesGenerado' => 0,
                    'fecha_pago' => date('Y-m-d H:i:s'),
                    'descripcion' => $descripcion,
                    'cantAbono' => sprintf('%.2f', $cobro->monto_cuota)
                ]);

                // Insertar historial
                $modelHistorialCobro->insert([
                    'abono' => sprintf('%.2f', $cobro->monto_cuota),
                    'id_cobro' => $cobro->id_cobro,
                    'descripcion' => $descripcion,
                    'id_factura' => $idFactura
                ]);
            }

            $db->transComplete(); // Finalizar transacción

            if ($db->transStatus() === false) {
                throw new \Exception('Ocurrió un error al confirmar la transacción.');
            }

            // Generar documento
            $rutaGenerada = $this->generarDocPagosContado($idFactura, $solicitud["numero_solicitud"], $productosXsoliContado);

            return $rutaGenerada ?: false; // Devuelve la ruta o false si está vacía

        } catch (\Throwable $th) {
            $db->transRollback();
            log_message('error', 'Error al cancelar la solicitud: ' . $th->getMessage());
            return false;
        }
    }

    public function generarDocPagosContado($idFactCreada, $solicitudNu, $dataContado)
    {
        log_message("info", "***************************************generarDocPagosContado***************************************");
        log_message("info", "el valor dsolicitudNu:: " . $solicitudNu);
        $session = session();
        try {
            $facturasModel = new FacturasModel();
            $modelSolicitud = new SolicitudModel();

            // Obtener historial de pagos de la factura
            $historialArray = $facturasModel->obtenerHistorialCobros($idFactCreada);
            if (!$historialArray) {
                log_message("info", "El historial de pagos está vacío.");
            }

            // Obtener datos del cliente y contrato
            $datosCobros = $modelSolicitud->getDatosCobrosC($solicitudNu);
            log_message("info", "valor del datosCobros:: " . print_r($datosCobros, true));
            if (empty($datosCobros)) {
                return $this->response->setJSON([
                    'ok' => false,
                    'message' => 'No se encontraron cobros pendientes para este registro.'
                ]);
            }

            $templatePath = FCPATH . 'public/documentos/pagos/Formato_Factura_contado.docx';
            if (!file_exists($templatePath)) {
                throw new \Exception('El archivo de plantilla no se encuentra en la ruta especificada.');
            }

            $templateProcessor = new TemplateProcessor($templatePath);

            // Variables de acumulación
            $totalMonto = 0;
            $numCuot = '';
            $numeroFilas = count($dataContado);
            $templateProcessor->cloneRow('descripcion', $numeroFilas);

            foreach ($dataContado as $index => $producto) {
                $numCuot .= ($numCuot !== '' ? '_' : '') . ($index + 1);

                $descripcion = ucfirst(mb_strtolower($producto['nombre'], 'UTF-8'));
                $cantidad = $producto['cantidad_producto'];
                $precio = $producto['p_contado'];
                $total = $cantidad * $precio;

                $templateProcessor->setValue("descripcion#" . ($index + 1), $descripcion);
                $templateProcessor->setValue("codigo#" . ($index + 1), $producto['codigo_producto']);
                $templateProcessor->setValue("cant#" . ($index + 1), $cantidad);
                $templateProcessor->setValue("pUni#" . ($index + 1), '$' . number_format($precio, 2));
                $templateProcessor->setValue("totalU#" . ($index + 1), '$' . number_format($total, 2));

                $totalMonto += $total;
            }


            // Total en letras
            $templateProcessor->setValue("sumaTotal", '$' . number_format($totalMonto, 2));
            $totalEnLetras = $this->convertirNumeroALetras($totalMonto);
            $templateProcessor->setValue("totalApagarLetras", $totalEnLetras);

            // Asignar datos del cliente y contrato
            $templateProcessor->setValue("nombreCliente", $datosCobros[0]['nombre_completo']);
            $templateProcessor->setValue("noContrato", $datosCobros[0]['no_factura']);


            // Crear carpeta de destino
            $rutaCarpeta = FCPATH . 'public/documentos/pagos/' . $solicitudNu . '/';
            if (!is_dir($rutaCarpeta)) {
                mkdir($rutaCarpeta, 0755, true);
            }

            /* $rutaArchivo = $rutaCarpeta . 'pagoCuota' . $idFactCreada . '_' . $numCuot . '.docx';
            $rutaArchivoRetorno = 'public/documentos/pagos/' . $idFactCreada . '/pagoCuota' . $idFactCreada . '_' . $numCuot . '.docx'; */
            $rutaArchivo = $rutaCarpeta . 'pagoCuota' . $solicitudNu . '_' . $numCuot . '.docx';
            $rutaArchivoRetorno = 'public/documentos/pagos/' . $solicitudNu . '/pagoCuota' . $solicitudNu . '_' . $numCuot . '.docx';
            $templateProcessor->saveAs($rutaArchivo);

            // Actualizar la ruta en la tabla factura
            $facturasModel->update($idFactCreada, ['ruta_factura' => $rutaArchivoRetorno]);

            log_message("info", "Documento generado exitosamente: {$rutaArchivoRetorno}");

            return $rutaArchivoRetorno;
        } catch (\Throwable $e) {
            log_message('error', 'Error en generarDocPagos: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error al generar el documento de pagos'
            ];
        }
    }
}
