<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'LoginController::index');
$routes->post('validateUser', 'LoginController::validateUser');
$routes->post('recuperarPassword', 'LoginController::recuperarPassword');
$routes->get('salir', 'LoginController::logout');

//rutas de acceso para administrador
$routes->get('inicio_administracion', 'AdministracionController::index');//ya esta
$routes->get('usuarios', 'UsuariosController::index');//ya esta
$routes->get('getUsuarios', 'UsuariosController::getUsuariosAll');//ya esta
$routes->post('postUsuarioNew', 'UsuariosController::nuevoUsuario');//ya esta
$routes->post('updateInfoUser', 'UsuariosController::actulizarInfoUsuario');//ya esta
$routes->post('actualizarPwdReset', 'UsuariosController::recuperarPwdAct');//ya esta
//actualmente no se usa
$routes->post('deleteUser', 'UsuariosController::deleteUsuario');


//rutas generales
$routes->get('perfil', 'AdministracionController::perfilUsuarioGeneral');
$routes->post('cambio_password', 'AdministracionController::actualizarContrasena');
$routes->post('actualizar_informacion', 'AdministracionController::actualizarDatosUsuario');

//rutas para vendedores o cajera
$routes->get('solicitudes', 'SolicitudesController::index');
$routes->get('nueva_solicitud', 'SolicitudesController::nuevaSolicitud');
$routes->post('procesar_nueva_sol', 'SolicitudesController::procesarSolicitud');
$routes->get('ver_solicitud', 'SolicitudesController::ver_solicitud');
$routes->get('clientes', 'ClientesController::index');
$routes->get('nuevo_cliente', 'ClientesController::nuevoCliente');
$routes->get('editar_cliente/(:any)', 'ClientesController::editar_cliente/$1');
$routes->post('guardar_cliente', 'ClientesController::guardarClientes');
$routes->post('editar_cliente', 'ClientesController::guardarClientes');
$routes->post('searchClient', 'ClientesController::buscarClientes');


//rutas de carga de departamento, es general
$routes->get('departamentos', 'DepartamentosController::getAllDepartamentos');
$routes->post('municipios', 'MunicipiosController::index');
$routes->post('distritos', 'DistritosController::index');
$routes->post('coloniasXDistrito', 'ColoniaController::getColoniaXDistrito');
//$routes->post('coloniasXmunicipio', 'ColoniaController::getColoniaXDistrito');
$routes->post('coloniascliente', 'ColoniaController::getColoniaCliente');

//rutas para productos
$routes->get('productos', 'ProductosController::index');
$routes->get('getProductos', 'ProductosController::getProductAll');
$routes->get('getProductosActivos', 'ProductosController::getProductActive');
$routes->post('saveProduct', 'ProductosController::nuevoProducto');
$routes->post('updateProduct', 'ProductosController::actualizarProducto');
$routes->post('ajusteProducto', 'ProductosController::ajustarProducto');
$routes->post('getProducts', 'ProductosController::buscarProductoIngCompra');
$routes->post('getProductoExistencia', 'ProductosController::buscarProductos');

//rutas para crear categorias
$routes->get('categorias', 'CategoriasController::index');
$routes->post('guardarCategoria', 'CategoriasController::nuevaCategoria');
$routes->post('updateInfoCategoria', 'CategoriasController::updateCategoria');
$routes->get('getCategorias', 'CategoriasController::getCategoriasAll');

//rutas para crear proveedores
$routes->get('proveedores', 'ProveedorController::index');
$routes->post('guardarProveedor', 'ProveedorController::nuevoProveedor');
$routes->get('getProveedor', 'ProveedorController::getProveedoresAll');
$routes->post('updateInfoProveedor', 'ProveedorController::updateProveedor');
$routes->get('getProveedorActivos', 'ProveedorController::getProveedoresAllActives');

//ingresos por compras
$routes->get('ingresos_por_compras', 'IngresoPorComprasController::index');
$routes->post('procesar_ingreso_x_compra', 'IngresoPorComprasController::registrarMovimientoIngreso');
$routes->get('obtenerRegDocumentos/(:num)', 'IngresoPorComprasController::obtenerDocumentos/$1');
$routes->get('obtenerRegDocumentos', 'IngresoPorComprasController::obtenerDocumentos');
$routes->post('generaPDfInXCompra', 'IngresoPorComprasController::generarPdf');

//ingresos 
$routes->get('ingresos', 'IngresosController::index');
//para ingresos y salidas
$routes->post('procesar_ingreso', 'IngresosController::ingresar_movimiento');

//salidas
$routes->get('salidas', 'SalidasController::index');

//comisiones
$routes->get('comisiones', 'ComisionesController::getDatos');

//apoderado
$routes->get('apoderados','ApoderadosController::index');
$routes->post('apoderadosUpdate','ApoderadosController::apoderadosUpdate');
$routes->post('apoderadosNew','ApoderadosController::apoderadosNew');
$routes->get('getApoderado','ApoderadosController::getApoderados');

$routes->get('documentos','GenerarSolicitudCreditoController::generarDocumento');
$routes->get('generar_contrato/(:num)', 'GenerarSolicitudCreditoController::generarContrato/$1');

$routes->get('permisos','PermisosPerfilesController::index');
$routes->get('getPerfiles','PermisosPerfilesController::getperfiles');

$routes->get('getAccesos','PermisosPerfilesController::getAccesos');
$routes->post('editPermisos','PermisosPerfilesController::asignarPermisos');

$routes->post('actualizarEstado','SolicitudesController::actualizarEstado');
$routes->get('archivo/descargar/(:any)', 'GenerarSolicitudCreditoController::descargar/$1');

$routes->get('profesiones','ProfesionController::index');
$routes->post('guardarProfesion','ProfesionController::save');
$routes->get('obtenerProfesiones','ProfesionController::obtenerProfesiones');

$routes->get('colonias','ColoniaController::index');
$routes->post('getColonias','ColoniaController::getColonias');
$routes->post('colonias','ColoniaController::saveColonia');

$routes->get('cobros','CobrosController::index');
$routes->post('getCobrosCliente','CobrosController::getCobrosClientes');
$routes->post('getDeudasPorSolicitud','CobrosController::getDeudasPorSolicitud');
$routes->post('procesarPagosCuotas','CobrosController::procesarPagos');
$routes->post('downloadDocPago','CobrosController::descargarDocumentoCobros');