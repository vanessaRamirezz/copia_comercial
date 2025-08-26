<?php

namespace App\Controllers;

use App\Models\ApoderadosModel;

class ApoderadosController extends BaseController
{
    private $nameClass = "ApoderadosController";
    public function index()
    {
        $session = session();
        if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
            $url = $this->request->uri->getSegment(1);
            log_message("info", $this->nameClass . " " . $url);
            $accesos = json_decode($_SESSION['accesos'], true);
            $allowedUrls = array_column($accesos, 'url_acceso');
            if (in_array($url, $allowedUrls)) {
                $content4 = view('apoderados/apoderados');
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

    public function getApoderados()
    {
        try {
            $session = session();
            if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
                $modelApoderado = new ApoderadosModel();
                $data = $modelApoderado->getApoderados();
                log_message('info', 'Datos a mostrar: ' . print_r($data, true));
                // Estructuramos la respuesta para incluir un indicador de éxito
                log_message('info', 'retorno');
                echo json_encode(['success' => true, 'data' => $data]);
            } else {
                return redirect()->to(base_url());
            }
        } catch (\Throwable $e) {
            $errorMessage = 'Ocurrió un error: ' . $e->getMessage() . PHP_EOL;
            $errorMessage .= 'Trace: ' . $e->getTraceAsString();

            log_message('error', $errorMessage);
            echo json_encode(['error' => "Al parecer hay un error al recuperar los datos."]);
        }
    }

    public function apoderadosNew()
    {
        $model = new ApoderadosModel();

        // Iniciar una transacción para asegurar que las operaciones se completen exitosamente
        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            // Desactivar todos los apoderados existentes
            $db->query('UPDATE apoderados SET estado = 0');

            // Insertar el nuevo apoderado con estado activo (1)
            $data = [
                'dui_apoderado' => $this->request->getPost('duiApoderado'),
                'nombre_apoderado' => $this->request->getPost('nombreApoderado'),
                'representante_legal' => $this->request->getPost('nombreRepreLegal'),
                'dui_representante' => $this->request->getPost('duiRepre'),
                'estado' => 1,
                'fecha_nacimiento_apoderado' => $this->request->getPost('fecha_nacimiento_apoderado'),
                'fecha_nacimiento_rLegal' => $this->request->getPost('fecha_nacimiento_rLegal')
            ];

            if ($model->insert($data)) {
                // Confirmar la transacción si todo fue exitoso
                $db->transCommit();
                return $this->response->setJSON(['success' => 'El apoderado ha sido creado exitosamente y los anteriores han sido desactivados.']);
            } else {
                // Revertir la transacción si hubo un error al insertar el nuevo apoderado
                $db->transRollback();
                return $this->response->setJSON(['error' => 'No se pudo crear el apoderado.']);
            }
        } catch (\Exception $e) {
            // Revertir la transacción en caso de cualquier excepción
            $db->transRollback();
            $errorMessage = 'Ocurrió un error: ' . $e->getMessage() . PHP_EOL;
            $errorMessage .= 'Trace: ' . $e->getTraceAsString();

            log_message('error', $errorMessage);
            return $this->response->setJSON(['error' => 'Ocurrió un error al intentar guardar el apoderado.']);
        }
    }



    public function apoderadosUpdate()
    {
        try {
            $model = new ApoderadosModel();
            $idapoderado = $this->request->getPost('idapoderado');
            $data = [
                'dui_apoderado' => $this->request->getPost('duiApoderado'),
                'nombre_apoderado' => $this->request->getPost('nombreApoderado'),
                'representante_legal' => $this->request->getPost('nombreRepreLegal'),
                'dui_representante' => $this->request->getPost('duiRepre'),
                'fecha_nacimiento_apoderado' => $this->request->getPost('fecha_nacimiento_apoderado'),
                'fecha_nacimiento_rLegal' => $this->request->getPost('fecha_nacimiento_rLegal')
            ];

            if ($model->update($idapoderado, $data)) {
                return $this->response->setJSON(['success' => 'El apoderado ha sido actualizado exitosamente.']);
            } else {
                return $this->response->setJSON(['error' => 'No se pudo actualizar el apoderado.']);
            }
        } catch (\Throwable $e) {
            $errorMessage = 'Ocurrió un error: ' . $e->getMessage() . PHP_EOL;
            $errorMessage .= 'Trace: ' . $e->getTraceAsString();

            log_message('error', $errorMessage);
        }
    }
}
