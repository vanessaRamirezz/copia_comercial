<?php

namespace App\Controllers;

use App\Models\RpVentasMensualesModel;
use App\Models\SucursalesModel;
use App\Models\UsuariosModel;

class RpVentasXvendedorController extends BaseController
{
    private $nameClass = "RpVentasXvendedorController";

    public function index()
    {
        $session = session();
        if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
            $url = $this->request->uri->getSegment(1);
            log_message("info", $this->nameClass . " " . $url);
            $accesos = json_decode($_SESSION['accesos'], true);
            $allowedUrls = array_column($accesos, 'url_acceso');
            if (in_array($url, $allowedUrls)) {

                $dataSucursales = new SucursalesModel();
                $sucursales = $dataSucursales->getSucursalesAll();

                $UserModels = new UsuariosModel();
                $dataUsers = $UserModels->getUsuarios();

                // Pasar los datos a la vista
                $data = [
                    'sucursales' => $sucursales,
                    'usuarios' => $dataUsers
                ];

                $content4 = view('reportes/rpVentasXvendedor', $data);
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

    public function buscarDatos()
    {
        $session = session();

        if (!$this->request->isAJAX()) {
            log_message('error', 'Acceso no permitido: solicitud no AJAX');
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acceso no permitido']);
        }

        $fechaInicio = $this->request->getPost('fecha_inicio');
        $fechaFin = $this->request->getPost('fecha_fin');
        $vendedor = $this->request->getPost('vendedor');

        log_message('debug', 'Fecha Inicio: {fechaInicio}', ['fechaInicio' => $fechaInicio]);
        log_message('debug', 'Fecha Fin: {fechaFin}', ['fechaFin' => $fechaFin]);
        log_message('debug', 'Sucursal: {vendedor}', ['vendedor' => $vendedor]);

        if (!$vendedor || !$fechaInicio || !$fechaFin) {
            log_message('error', 'Datos incompletos: falta alguno de los campos requeridos');
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Datos incompletos']);
        }

        $rpVentasMensuales = new RpVentasMensualesModel();
        $resultado = $rpVentasMensuales->getReporteVentasXvendedor($fechaInicio, $fechaFin, $vendedor);

        log_message('debug', 'Resultado obtenido: {resultado}', ['resultado' => json_encode($resultado)]);

        return $this->response->setJSON($resultado);
    }
}
