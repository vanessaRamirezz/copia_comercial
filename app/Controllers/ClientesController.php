<?php

namespace App\Controllers;

use App\Models\ClientesModel;

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
                    'id_sucursal_creacion' => $_SESSION['sucursal']
                ];
                $clientesModel = new ClientesModel();
                log_message("info", "Valor de datos a guarda::: " . print_r($data, true));

                if ($idPersonaEditar) {
                    // 🔄 Editar cliente
                    if ($clientesModel->actualizarCliente($idPersonaEditar, $data)) {
                        echo json_encode(['success' => 'Datos actualizados correctamente']);
                    } else {
                        echo json_encode(['error' => 'Ocurrió un error al intentar actualizar los datos']);
                    }
                } else {
                    $dui = (string) $this->request->getPost('duiPersonal');
                    if ($clientesModel->existeDui($dui)) {
                        echo json_encode(['error' => 'El DUI ingresado ya existe']);
                    } else {
                        if ($clientesModel->guardarCliente($data)) {
                            echo json_encode(['success' => 'Datos registrados correctamente']);
                        } else {
                            echo json_encode(['error' => 'ocurrio un error al intentar insertar los datos']);
                        }
                    }
                }
            } else {
                return redirect()->to(base_url());
            }
        } catch (\Throwable $th) {
            log_message('ERROR', 'Error al intentar guardar ' . $th);
        }
    }

    public function buscarClientes()
    {
        try {
            $session = session();
            if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
                $clientesModel = new ClientesModel();

                $dui = $this->request->getBody(); // Asegúrate de que el nombre del parámetro POST es 'dui'
                $clienteEncontrado = $clientesModel->buscarCliente($dui);

                if ($clienteEncontrado) {
                    return $this->response->setJSON($clienteEncontrado);
                } else {
                    return $this->response->setJSON(['error' => 'El DUI ingresado no existe.']);
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
}
