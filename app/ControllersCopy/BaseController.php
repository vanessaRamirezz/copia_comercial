<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Controllers\MenuController;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = [];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = \Config\Services::session();
    }


    protected function renderPage(string $content4): string
    {
        $session = session();
        if (isset($_SESSION['sesion_activa'])) {
            // Renderiza las vistas predefinidas del encabezado, barra lateral, barra superior y pie de página
            $MenuController = new MenuController();
            $data['dataMenu'] = $MenuController->obtenerMenuPorUsuario();
            $content1 = view('head_footer/header');
            $content2 = view('head_footer/sidebar',$data);
            $content3 = view('head_footer/topbar');
            $content5 = view('head_footer/footer');

            // Combina el contenido predefinido con el contenido específico
            $fullContent = $content1 . $content2 . $content3 . $content4 . $content5;

            // Devuelve la página completa
            return $fullContent;
        }else {
            return redirect()->to(base_url());
        }
    }
}
