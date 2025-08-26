<?php
namespace App\Controllers;
use App\Models\ComisionesModel;

class ComisionesController extends BaseController
{
    public function getDatos()
    {
        $datosComision = new ComisionesModel();
        $datos = $datosComision->getComisiones();
        $response = [
            'comisiones' => array_map(function($item) {
                return [
                    'cantidad_meses' => $item['cantidad_meses'],
                    'valor' => $item['valor']
                ];
            }, $datos)
        ];

        return $this->response->setJSON($response);
    }
}
