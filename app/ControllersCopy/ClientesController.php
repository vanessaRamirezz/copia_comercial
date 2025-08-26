<?php

namespace App\Controllers;

use App\Models\ClientesModel;
use CodeIgniter\Images\ImageLib;
use CodeIgniter\I18n\Time;

class ClientesController extends BaseController
{
    private $nameClass = "ClientesController";
    public function index()
    {
        $session = session();
        if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
            $clientesModel = new ClientesModel();
            $url = $this->request->uri->getSegment(1);
            log_message("info", $this->nameClass . " " . $url);
            $accesos = json_decode($_SESSION['accesos'], true);
            $allowedUrls = array_column($accesos, 'url_acceso');
            if (in_array($url, $allowedUrls)) {
                $data['dataClientes'] = $clientesModel->getClientes();
                $content4 = view('clientes/clientes', $data);
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

    public function nuevoCliente()
    {
        $session = session();
        if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
            $url = $this->request->uri->getSegment(1);
            log_message("info", $this->nameClass . " " . $url);
            $accesos = json_decode($_SESSION['accesos'], true);
            $allowedUrls = array_column($accesos, 'url_acceso');
            if (in_array($url, $allowedUrls)) {
                $content4 = view('clientes/nuevo_cliente');
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

    public function guardarClientes()
{
    try {
        $session = session();
        if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
            $url = $this->request->uri->getSegment(1);
            log_message("info", $this->nameClass . " " . $url);

            $idPersonaEditar = $this->request->getPost('idPersonaEditar');

            $duiClienteFront = $this->request->getFile('duiClienteFront');
            $duiClienteRever = $this->request->getFile('duiClienteReversa');

            $rutaFrontal = '';
            $rutaReversa = '';

            $clientesModel = new ClientesModel();
            $clienteExistente = null;

            if ($idPersonaEditar) {
                $clienteExistente = $clientesModel->obtenerClientePorId($idPersonaEditar);
            }

            // Verificamos si se cargaron nuevas imágenes para el DUI
            if ($duiClienteFront && $duiClienteFront->isValid() && !$duiClienteFront->hasMoved()) {
                // Si el usuario cargó una nueva imagen para la parte frontal
                $dui = trim((string)$this->request->getPost('duiPersonal'));

                if (empty($dui)) {
                    throw new \Exception("El DUI no puede estar vacío al construir la ruta.");
                }

                log_message('info', 'DUI recibido para convertir a base64: ' . $dui);

                // Procesar y reducir el tamaño de la imagen frontal
                $imageFront = imagecreatefromjpeg($duiClienteFront->getTempName()); // Usamos imagecreatefromjpeg si la imagen es JPEG
                $width = imagesx($imageFront);
                $height = imagesy($imageFront);

                $newWidth = $width / 2;  // Reducir el tamaño al 50%
                $newHeight = $height / 2;

                $newImage = imagecreatetruecolor($newWidth, $newHeight);
                imagecopyresampled($newImage, $imageFront, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                // Guardar la imagen reducida en un archivo temporal
                ob_start(); // Inicia el buffer de salida
                imagejpeg($newImage); // Escribe la imagen en el buffer
                $imageData = ob_get_clean(); // Obtiene los datos de la imagen desde el buffer
                $base64ImageFront = base64_encode($imageData); // Codifica directamente desde memoria


                // Asignar la imagen frontal en base64
                $rutaFrontal = $base64ImageFront;

                // Liberar la memoria de la imagen
                imagedestroy($imageFront);
                imagedestroy($newImage);
            } else {
                // Si no se cargó nueva imagen para la parte frontal, mantenemos la imagen actual
                $rutaFrontal = $clienteExistente['duiFrontal'] ?? null;
            }

            if ($duiClienteRever && $duiClienteRever->isValid() && !$duiClienteRever->hasMoved()) {
                // Si el usuario cargó una nueva imagen para la parte reversa
                $imageRever = imagecreatefromjpeg($duiClienteRever->getTempName()); // Usamos imagecreatefromjpeg si la imagen es JPEG
                $width = imagesx($imageRever);
                $height = imagesy($imageRever);

                $newWidth = $width / 2;  // Reducir el tamaño al 50%
                $newHeight = $height / 2;

                $newImage = imagecreatetruecolor($newWidth, $newHeight);
                imagecopyresampled($newImage, $imageRever, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                // Guardar la imagen reducida en un archivo temporal
                ob_start(); // Inicia el buffer de salida
                imagejpeg($newImage); // Escribe la imagen en el buffer
                $imageData = ob_get_clean(); // Obtiene los datos de la imagen desde el buffer
                $base64ImageRever = base64_encode($imageData); // Codifica directamente desde memoria


                // Asignar la imagen reversa en base64
                $rutaReversa = $base64ImageRever;

                // Liberar la memoria de la imagen
                imagedestroy($imageRever);
                imagedestroy($newImage);
            } else {
                // Si no se cargó nueva imagen para la parte reversa, mantenemos la imagen actual
                $rutaReversa = $clienteExistente['duiReversa'] ?? null;
            }

            // Si solo se cambió la reversa, la frontal debe mantenerse igual
            if (empty($rutaFrontal) && !empty($rutaReversa)) {
                $rutaFrontal = $clienteExistente['duiFrontal'] ?? null;
            }

            // Si solo se cambió la frontal, la reversa debe mantenerse igual
            if (!empty($rutaFrontal) && empty($rutaReversa)) {
                $rutaReversa = $clienteExistente['duiReversa'] ?? null;
            }

            // Datos a guardar en la base de datos
            $data = [
                'dui' => $this->request->getPost('duiPersonal'),
                'nombre_completo' => $this->request->getPost('nombrePersonalCN'),
                'estado_civil' => $this->request->getPost('estadoCivilCN'),
                'fecha_nacimiento' => $this->request->getPost('fechaNacimientoCN'),
                'telefono' => $this->request->getPost('telPersonal'),
                'direccion' => $this->request->getPost('direccionActualCN'),
                'departamento' => $this->request->getPost('deptoClienteCN'),
                'municipio' => $this->request->getPost('muniClienteCN'),
                'distrito' => $this->request->getPost('distritoClienteCN'),
                'colonia' => $this->request->getPost('coloniaClienteCN'),
                'correo' => $this->request->getPost('correoCN'),
                'nombre_conyugue' => $this->request->getPost('nombreConyugueCN'),
                'direccion_trabajo_conyugue' => $this->request->getPost('dirTrabajoConyugueCN'),
                'telefono_trabajo_conyugue' => $this->request->getPost('telTrabajoConyugueCN'),
                'nombre_padres' => $this->request->getPost('nombresPadresCN'),
                'direccion_padres' => $this->request->getPost('direccionDeLosPadresCN'),
                'telefono_padres' => $this->request->getPost('telPadresCN'),
                'CpropiaCN' => $this->request->getPost('CpropiaCN'),
                'CpromesaVentaCN' => $this->request->getPost('CpromesaVentaCN'),
                'CalquiladaCN' => $this->request->getPost('CalquiladaCN'),
                'aQuienPerteneceCN' => $this->request->getPost('aQuienPerteneceCN'),
                'telPropietarioCN' => $this->request->getPost('telPropietarioCN'),
                'tiempoDeVivirDomicilioCN' => $this->request->getPost('tiempoDeVivirDomicilioCN'),
                'id_user_creacion' => $_SESSION['id_usuario'],
                'id_sucursal_creacion' => $_SESSION['sucursal'],
                'duiFrontal' => $rutaFrontal,
                'duiReversa' => $rutaReversa
            ];

            // Guardar datos en la base de datos
            if ($idPersonaEditar) {
                log_message('info', 'Datos a actualizar: ' . json_encode($data));
                if ($clientesModel->actualizarCliente($idPersonaEditar, $data)) {
                    echo json_encode(['success' => 'Datos actualizados correctamente']);
                } else {
                    echo json_encode(['error' => 'Ocurrió un error al intentar actualizar los datos']);
                }
            } else {
                $dui = (string)$this->request->getPost('duiPersonal');

                if ($clientesModel->existeDui($dui)) {
                    echo json_encode(['error' => 'El DUI ingresado ya existe']);
                } else {
                    if ($clientesModel->guardarCliente($data)) {
                        echo json_encode(['success' => 'Datos registrados correctamente']);
                    } else {
                        echo json_encode(['error' => 'Ocurrió un error al intentar insertar los datos']);
                    }
                }
            }
        } else {
            return redirect()->to(base_url());
        }
    } catch (\Throwable $th) {
        log_message('error', 'Error al intentar guardar: ' . $th->getMessage());
        echo json_encode(['error' => 'Ocurrió una excepción inesperada']);
    }
}
    public function guardarClientesCopia()
    {
        try {
            $session = session();
            if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
                $url = $this->request->uri->getSegment(1);
                log_message("info", $this->nameClass . " " . $url);

                $idPersonaEditar = $this->request->getPost('idPersonaEditar');

                $duiClienteFront = $this->request->getFile('duiClienteFront');
                $duiClienteRever = $this->request->getFile('duiClienteReversa');

                $rutaFrontal = '';
                $rutaReversa = '';

                $clientesModel = new ClientesModel();
                $clienteExistente = null;

                if ($idPersonaEditar) {
                    $clienteExistente = $clientesModel->obtenerClientePorId($idPersonaEditar);
                }

                // Verificamos si las imágenes del DUI son válidas
                if ($duiClienteFront && $duiClienteFront->isValid() && !$duiClienteFront->hasMoved()) {
                    $dui = trim((string)$this->request->getPost('duiPersonal'));

                    if (empty($dui)) {
                        throw new \Exception("El DUI no puede estar vacío al construir la ruta.");
                    }

                    log_message('info', 'DUI recibido para crear carpeta: ' . $dui);
                    log_message('info', 'WRITEPATH: ' . WRITEPATH);

                    $basePath = FCPATH . 'public/img/usuarios/' . $dui . '/'; // Cambié WRITEPATH por FCPATH y ajusté la ruta.

                    // Eliminar imágenes anteriores si existen
                    if ($clienteExistente) {
                        $rutaFrontalAnterior = FCPATH . 'public/' . $clienteExistente['duiFrontal'];
                        $rutaReversaAnterior = FCPATH . 'public/' . $clienteExistente['duiReversa'];

                        if (file_exists($rutaFrontalAnterior)) {
                            unlink($rutaFrontalAnterior);
                        }
                        if (file_exists($rutaReversaAnterior)) {
                            unlink($rutaReversaAnterior);
                        }
                    }

                    // Crear carpetas si no existen
                    $pathFront = $basePath . 'dui_frontal/';
                    $pathRever = $basePath . 'dui_reversa/';

                    log_message('info', 'Intentando crear carpeta: ' . $pathFront);

                    if (!is_dir($pathFront)) {
                        if (!mkdir($pathFront, 0777, true)) {
                            throw new \Exception("No se pudo crear la carpeta: $pathFront");
                        } else {
                            log_message('info', 'Carpeta frontal creada: ' . $pathFront);
                        }
                    }

                    if (!is_dir($pathRever)) {
                        if (!mkdir($pathRever, 0777, true)) {
                            throw new \Exception("No se pudo crear la carpeta: $pathRever");
                        } else {
                            log_message('info', 'Carpeta reversa creada: ' . $pathRever);
                        }
                    }

                    // Nombre de los archivos
                    $nombreFront = 'dui_front_' . time() . '.' . $duiClienteFront->getExtension();
                    $nombreRever = 'dui_rever_' . time() . '.' . $duiClienteRever->getExtension();

                    // Mover las imágenes
                    if (!$duiClienteFront->move($pathFront, $nombreFront)) {
                        throw new \Exception("No se pudo mover la imagen frontal del DUI.");
                    }
                    if (!$duiClienteRever->move($pathRever, $nombreRever)) {
                        throw new \Exception("No se pudo mover la imagen reversa del DUI.");
                    }

                    // Asignar las rutas finales
                    $rutaFrontal = 'img/usuarios/' . $dui . '/dui_frontal/' . $nombreFront;
                    $rutaReversa = 'img/usuarios/' . $dui . '/dui_reversa/' . $nombreRever;
                } else {
                    // Si no hay cambios, mantener las rutas actuales
                    $rutaFrontal = $clienteExistente['duiFrontal'] ?? null;
                    $rutaReversa = $clienteExistente['duiReversa'] ?? null;
                }

                // Datos a guardar en la base de datos
                $data = [
                    'dui' => $this->request->getPost('duiPersonal'),
                    'nombre_completo' => $this->request->getPost('nombrePersonalCN'),
                    'estado_civil' => $this->request->getPost('estadoCivilCN'),
                    'fecha_nacimiento' => $this->request->getPost('fechaNacimientoCN'),
                    'telefono' => $this->request->getPost('telPersonal'),
                    'direccion' => $this->request->getPost('direccionActualCN'),
                    'departamento' => $this->request->getPost('deptoClienteCN'),
                    'municipio' => $this->request->getPost('muniClienteCN'),
                    'distrito' => $this->request->getPost('distritoClienteCN'),
                    'colonia' => $this->request->getPost('coloniaClienteCN'),
                    'correo' => $this->request->getPost('correoCN'),
                    'nombre_conyugue' => $this->request->getPost('nombreConyugueCN'),
                    'direccion_trabajo_conyugue' => $this->request->getPost('dirTrabajoConyugueCN'),
                    'telefono_trabajo_conyugue' => $this->request->getPost('telTrabajoConyugueCN'),
                    'nombre_padres' => $this->request->getPost('nombresPadresCN'),
                    'direccion_padres' => $this->request->getPost('direccionDeLosPadresCN'),
                    'telefono_padres' => $this->request->getPost('telPadresCN'),
                    'CpropiaCN' => $this->request->getPost('CpropiaCN'),
                    'CpromesaVentaCN' => $this->request->getPost('CpromesaVentaCN'),
                    'CalquiladaCN' => $this->request->getPost('CalquiladaCN'),
                    'aQuienPerteneceCN' => $this->request->getPost('aQuienPerteneceCN'),
                    'telPropietarioCN' => $this->request->getPost('telPropietarioCN'),
                    'tiempoDeVivirDomicilioCN' => $this->request->getPost('tiempoDeVivirDomicilioCN'),
                    'id_user_creacion' => $_SESSION['id_usuario'],
                    'id_sucursal_creacion' => $_SESSION['sucursal'],
                    'duiFrontal' => $rutaFrontal,
                    'duiReversa' => $rutaReversa
                ];

                // Guardar datos en la base de datos
                if ($idPersonaEditar) {
                    if ($clientesModel->actualizarCliente($idPersonaEditar, $data)) {
                        echo json_encode(['success' => 'Datos actualizados correctamente']);
                    } else {
                        echo json_encode(['error' => 'Ocurrió un error al intentar actualizar los datos']);
                    }
                } else {
                    $dui = (string)$this->request->getPost('duiPersonal');

                    if ($clientesModel->existeDui($dui)) {
                        echo json_encode(['error' => 'El DUI ingresado ya existe']);
                    } else {
                        if ($clientesModel->guardarCliente($data)) {
                            echo json_encode(['success' => 'Datos registrados correctamente']);
                        } else {
                            echo json_encode(['error' => 'Ocurrió un error al intentar insertar los datos']);
                        }
                    }
                }
            } else {
                return redirect()->to(base_url());
            }
        } catch (\Throwable $th) {
            log_message('error', 'Error al intentar guardar: ' . $th->getMessage());
            echo json_encode(['error' => 'Ocurrió una excepción inesperada']);
        }
    }


    public function buscarClientes()
    {
        try {
            $session = session();
            if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
                $clientesModel = new ClientesModel();

                $dui = $this->request->getBody(); // Asegúrate de que el nombre del parámetro POST es 'dui'

                if (!ctype_digit(str_replace('-', '', $dui))) {

                    $clientes = $clientesModel->buscarPorNombreLike($dui);

                    return $this->response->setJSON($clientes);
                } else {

                    $clienteEncontrado = $clientesModel->buscarCliente($dui);

                    if ($clienteEncontrado) {
                        return $this->response->setJSON($clienteEncontrado);
                    } else {
                        return $this->response->setJSON(['error' => 'El DUI ingresado no existe.']);
                    }
                }
            } else {
                return $this->response->setJSON(['error' => 'No tiene permisos para ejecutar esta acción']);
            }
        } catch (\Throwable $th) {
            log_message('ERROR', 'Error al intentar guardar ' . $th);
            return $this->response->setJSON(['error' => 'Se produjo un error inesperado']);
        }
    }



    public function editar_cliente($param)
    {
        $session = session();
        if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
            $clientesModel = new ClientesModel();
            $decodedParam = base64_decode($param);

            $id_cliente = (int) $decodedParam;
            $datosClientes = $clientesModel->buscarCliente(null, $id_cliente);
            $data = [
                'datosClientes' => $datosClientes
            ];

            $content4 = view('clientes/nuevo_cliente', $data);
            $fullPage = $this->renderPage($content4);
            return $fullPage;
        } else {
            return redirect()->to(base_url());
        }
    }


    public function mostrarDui($tipo, $dui, $archivo)
    {
        log_message('info', "Mostrar DUI -> tipo: {$tipo}, dui: {$dui}, archivo: {$archivo}");

        $session = session();

        // Verificamos que esté logueado
        if (!isset($_SESSION['sesion_activa']) || $_SESSION['sesion_activa'] !== true) {
            log_message('info', 'Usuario no autenticado intentando acceder a la imagen.');
            return redirect()->to(base_url()); // O devolver un 403 si prefieres
        }

        // Cambiar la ruta para usar public/img/usuarios/
        $ruta = FCPATH . 'public/img/usuarios/' . $dui . '/' . $tipo . '/' . $archivo;

        log_message('info', "Ruta generada: {$ruta}");

        if (!is_file($ruta)) {
            log_message('error', 'Archivo no encontrado en ruta: ' . $ruta);
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("No se encontró la imagen.");
        }

        // Detectamos el tipo MIME (jpg, png, etc.)
        $mime = mime_content_type($ruta);
        log_message('info', "MIME detectado: {$mime}");

        // Enviamos la imagen al navegador con encabezado correcto
        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setBody(file_get_contents($ruta));
    }
}
