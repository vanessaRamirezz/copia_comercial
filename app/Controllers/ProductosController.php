<?php

namespace App\Controllers;

use App\Controllers\ProveedorController;
use App\Controllers\CategoriasController;
use App\Models\ProductosModel;
use App\Models\MovimientosModel;
use App\Models\SucursalesModel;
use App\Models\DisponibilidadProductosModel;

class ProductosController extends BaseController
{
    private $nameClass = "ProductosController";

    public function index()
    {
        $session = session();
        if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
            $url = $this->request->uri->getSegment(1);
            log_message("info", $this->nameClass . " " . $url);
            $accesos = json_decode($_SESSION['accesos'], true);
            $allowedUrls = array_column($accesos, 'url_acceso');
            if (in_array($url, $allowedUrls)) {
                $proveedoresController = new ProveedorController();
                $categoriasController = new CategoriasController();

                $proveedoresActivos = $proveedoresController->getProveedoresAllActives();
                $categoriasActivas = $categoriasController->getCategoriasAllActivas();

                $data = [
                    'proveedoresActivos' => $proveedoresActivos,
                    'categoriasActivas' => $categoriasActivas
                ];


                $content4 = view('productos/productos', $data);
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


    public function nuevoProducto()
    {
        try {
            $session = session();
            if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
                $productosModel = new ProductosModel();

                $codigo_producto = $this->request->getPost('codigo');
                $nombre = $this->request->getPost('nombre');
                $marca = $this->request->getPost('marca');
                $modelo = $this->request->getPost('modelo');
                $color = $this->request->getPost('color');
                $medidas = $this->request->getPost('medidas');
                $costo_unitario = $this->request->getPost('costo_unitario');
                $precio = $this->request->getPost('precio');
                $id_categoria = $this->request->getPost('id_categoria');
                $id_usuario_creacion = $_SESSION['id_usuario'];

                $data = [
                    'nombre' => $nombre,
                    'marca' => $marca,
                    'modelo' => $modelo,
                    'color' => $color,
                    'medidas' => $medidas,
                    'precio' => $precio,
                    'id_categoria' => $id_categoria,
                    'costo_unitario' => $costo_unitario,
                    'codigo_producto' => $codigo_producto,
                    'id_usuario_creacion' => $id_usuario_creacion
                ];
                if ($productosModel->insert($data)) {
                    echo json_encode(['success' => "Producto creado exitosamente."]);
                } else {
                    echo json_encode(['error' => "Al parecer ocurrio un error."]);
                }
            } else {
                return redirect()->to(base_url());
            }
        } catch (\Throwable $e) {
            // Otros errores de base de datos
            log_message('error', $e->getMessage());
            echo json_encode(['error' => "Ocurrió un error en la base de datos."]);
        }
    }

    public function actualizarProducto()
    {
        try {
            $session = session();
            if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
                $productosModel = new ProductosModel();

                $id_producto = $this->request->getPost('id_producto');

                $nombre = $this->request->getPost('nombre');
                $marca = $this->request->getPost('marca');
                $modelo = $this->request->getPost('modelo');
                $color = $this->request->getPost('color');
                $medidas = $this->request->getPost('medidas');
                $precio = $this->request->getPost('precio');
                $id_categoria = $this->request->getPost('id_categoria');
                $codigo_producto = $this->request->getPost('codigo');
                $estado = $this->request->getPost('estado');
                $costo_unitario = $this->request->getPost('costo_unitario');

                $data = [
                    'nombre' => $nombre,
                    'marca' => $marca,
                    'modelo' => $modelo,
                    'color' => $color,
                    'medidas' => $medidas,
                    'precio' => $precio,
                    'id_categoria' => $id_categoria,
                    'codigo_producto' => $codigo_producto,
                    'estado' => $estado,
                    'costo_unitario' => $costo_unitario
                ];
                if ($productosModel->update(['id_producto' => $id_producto], $data)) {
                    echo json_encode(['success' => "Producto actualizado exitosamente."]);
                } else {
                    echo json_encode(['error' => "Al parecer ocurrio un error."]);
                }
            } else {
                return redirect()->to(base_url());
            }
        } catch (\Throwable $e) {
            // Otros errores de base de datos
            log_message('error', $e->getMessage());
            echo json_encode(['error' => "Ocurrió un error en la base de datos."]);
        }
    }

    public function getProductAll()
    {
        try {
            $session = session();
            if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
                $productosModel = new ProductosModel();
                $dataRsp = $productosModel->getProductos();
                echo json_encode(['success' => $dataRsp]);
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

    public function getProductActive()
    {
        try {
            $session = session();
            if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
                $productosModel = new ProductosModel();
                $dataRsp = $productosModel->getProductosActivos();
                echo json_encode(['success' => $dataRsp]);
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


    public function ajustarProducto()
    {
        try {
            $session = session();
            if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
                $productosModel = new ProductosModel();
                $movimientoModel = new MovimientosModel();

                $id_producto = $this->request->getPost('id_producto');
                $tipoAjuste = $this->request->getPost('tipoAjuste');
                $cantidad_ajuste = $this->request->getPost('cantidad');
                $descripcion = $this->request->getPost('descripcion');

                $cantAjuste = 0;

                $producto = $productosModel->getProductosXid($id_producto);
                log_message("info", "Valor para ajustar el producto es: " . json_encode($producto));

                if ($producto) {
                    if ($tipoAjuste == 1) {
                        $cantAjuste = $producto[0]['stock'] + $cantidad_ajuste;
                        $dataProduct = ['stock' => $cantAjuste];

                        $rspProductos = $productosModel->update(['id_producto' => $id_producto], $dataProduct);
                        log_message("info", "La respuesta al actualizar la tabla productos es:::  " . $rspProductos);

                        $dataMovimientos = [
                            'id_tipo_movimiento' => $tipoAjuste,
                            'cantidad' => $cantAjuste,
                            'descripcion' => $descripcion,
                            'id_producto' => $id_producto
                        ];

                        if ($rspProductos) {
                            $rspMovimientos = $movimientoModel->insert($dataMovimientos);
                            if ($rspMovimientos) {
                                echo json_encode(['success' => "Ajuste realizado correctamente"]);
                            } else {
                                echo json_encode(['error' => "Ocurrio un error al insertar en la tabla movimientos"]);
                            }
                        } else {
                            echo json_encode(['error' => "Ocurrio un error al insertar en la tabla Productos"]);
                        }
                    } else if ($tipoAjuste == 2) {
                        $cantAjuste = $producto[0]['stock'] - $cantidad_ajuste;
                        $dataProduct = ['stock' => $cantAjuste];

                        $rspProductos = $productosModel->update(['id_producto' => $id_producto], $dataProduct);
                        log_message("info", "La respuesta al actualizar la tabla productos es:::  " . $rspProductos);

                        $dataMovimientos = [
                            'id_tipo_movimiento' => $tipoAjuste,
                            'cantidad' => $cantAjuste,
                            'descripcion' => $descripcion,
                            'id_producto' => $id_producto
                        ];

                        if ($rspProductos) {
                            $rspMovimientos = $movimientoModel->insert($dataMovimientos);
                            if ($rspMovimientos) {
                                echo json_encode(['success' => "Ajuste realizado correctamente"]);
                            } else {
                                echo json_encode(['error' => "Ocurrio un error al insertar en la tabla movimientos"]);
                            }
                        } else {
                            echo json_encode(['error' => "Ocurrio un error al insertar en la tabla Productos"]);
                        }
                    }
                } else {
                    echo json_encode(['error' => 'Producto no encontrado.']);
                }
            } else {
                return redirect()->to(base_url());
            }
        } catch (\Throwable $e) {
            // Otros errores de base de datos
            log_message('error', $e->getMessage());
            echo json_encode(['error' => "Ocurrió un error en la base de datos."]);
        }
    }

    public function buscarProductos()
    {
        try {
            log_message("info", "llego a buscarProductos con consulta");
            $session = session();
            if (isset($_SESSION['sesion_activa']) && $_SESSION['sesion_activa'] === true) {
                $search = $this->request->getPost('search');
                $sucursal_origen = $_SESSION['sucursal'];

                log_message("info", "Abuscar es:: " . $search . ' origen suc ' . $sucursal_origen);
                if (!empty($sucursal_origen)) {
                    $disponibilidadProductos = new DisponibilidadProductosModel();
                    # se saca el disponible
                    $resultDisponible = $disponibilidadProductos->getDisponibilidadProductosSucursal($search, $sucursal_origen);
                    
                    # se saca el total de las salidas por solicitud
                    $resultTotalSalidas = $disponibilidadProductos->getTotalSalidasPorSolicitud($search, $sucursal_origen);

                    $ajusteDisponibilidad = $this->nuevaDisponibilidad($resultDisponible, $resultTotalSalidas);

                    $productos = $ajusteDisponibilidad;
                } else {
                    $productosModel = new ProductosModel();
                    $productos = $productosModel->getProductosPorCodigoONombre($search);
                }
                echo json_encode(['success' => $productos]);
            } else {
                return redirect()->to(base_url());
            }
        } catch (\Throwable $e) {
            log_message('error', $e->getMessage());
            echo json_encode(['error' => "Ocurrió un error en la base de datos."]);
        }
    }

    public function nuevaDisponibilidad($resultDisponible, $resultTotalSalidas)
    {
        $salidasPorProducto = [];
        foreach ($resultTotalSalidas as $salida) {
            $salidasPorProducto[$salida->id_producto] = $salida->totalSalida;
        }

        foreach ($resultDisponible as &$disponible) {
            if (isset($salidasPorProducto[$disponible->id_producto])) {
                $disponible->disponibilidad -= $salidasPorProducto[$disponible->id_producto];
            }
        }
        log_message("info","Nuevo valor:: ".print_r($resultDisponible, true));
        return $resultDisponible;
    }
}
