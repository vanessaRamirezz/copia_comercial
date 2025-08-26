<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Productos</h1>
    <p class="mb-4">Se visualizaran todos los productos agregados.</p>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <button type="button" class="btn btn-primary" id="nuevoProductoBtn" data-toggle="modal" data-target="#nuevoProducto">
                Agregar producto
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTableProductos" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Codigo</th>
                            <th>UPC</th>
                            <th>Nombre producto</th>
                            <!-- <th>Disponible</th> -->
                            <th>Precio Uni</th>
                            <th>Costo Uni</th>
                            <th>Categoria</th>
                            <th>Estado</th>
                            <th>Usuario creacion</th>
                            <th>Fecha creacion</th>
                            <th>Opciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="nuevoProducto" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="nuevoProductoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="nuevoProductoLabel">Producto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="producto">
                    <input type="text" class="form-control" hidden id="id_producto" name="id_producto">
                    <div class="form-group row">
                        <div class="col-sm-4 mb-3 mb-sm-0">
                            <label for="codigo">Codigo:</label>
                            <input type="text" class="form-control" id="codigo" name="codigo">
                        </div>
                        <div class="col-sm-4 mb-3 mb-sm-0">
                            <label for="UPC">UPC:</label>
                            <input type="text" class="form-control" id="UPC" name="UPC">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-4 mb-3 mb-sm-0">
                            <label for="nombre">Nombre:</label>
                            <input type="text" class="form-control" id="nombre" name="nombre">
                        </div>
                        <div class="col-sm-4 mb-3 mb-sm-0">
                            <label for="marca">Marca:</label>
                            <input type="text" class="form-control" id="marca" name="marca">
                        </div>
                        <div class="col-sm-4 mb-3 mb-sm-0">
                            <label for="modeloProducto">Modelo:</label>
                            <input type="text" class="form-control" id="modeloProducto" name="modelo">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-3">
                            <label for="colorProducto">Color:</label>
                            <input type="text" class="form-control" id="colorProducto" name="color">
                        </div>
                        <div class="col-sm-6 mb-3 mb-sm-0">
                            <label for="medidasProducto">Medidas:</label>
                            <input type="text" class="form-control form-control-user" id="medidasProducto" name="medidas">
                        </div>
                        <div class="col-sm-3 mb-3 mb-sm-0">
                            <label for="costo_unitario">Costo unitario:</label>
                            <input type="text" class="form-control form-control-user montosG" id="costo_unitario" name="costo_unitario">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-3">
                            <label for="precioProducto">Precio unitario:</label>
                            <input type="text" class="form-control montosG" id="precioProducto" name="precio">
                        </div>
                        <div class="col-sm-3">
                            <label for="productoDisponible">Disponibilidad:</label>
                            <input type="text" class="form-control soloNumeros" id="productoDisponible" name="disponible">
                        </div>
                        <div class="col-sm-3 mb-3 mb-sm-0">
                            <label for="categoriaProducto">Categoria:</label>
                            <select class="form-control form-control-user" id="categoriaProducto" name="id_categoria">
                                <option value="-1">Seleccione...</option>
                                <?php if (!empty($categoriasActivas)) : ?>
                                    <?php foreach ($categoriasActivas as $categoria) : ?>
                                        <option value="<?php echo esc($categoria['id_categoria']); ?>"><?php echo esc($categoria['nombre']); ?></option>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <option value="-2">No hay categorías activas disponibles</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row estado">
                        <div class="col-sm">
                            <div class="row">
                                <div class="col-sm-3 mb-3 mb-sm-0">
                                    <label for="estado">Estado:</label>
                                    <select class="custom-select" id="estado" name="estado">
                                        <option value="-1" selected>Seleccione</option>
                                        <option value="1">Activo</option>
                                        <option value="0">Inactivo</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group row estado" style="display: contents;">
                                    <div class="col-sm-12 mt-4">
                                        <div class="alert alert-warning alertEstadoInactivo" role="alert" style="display: none; width: 100%;">
                                            Si el estado es inactivo, este producto no aparecera en la lista de seleccion para las solicitudes
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cerrarModalProducto">Cerrar</button>
                <button type="button" class="modalEditarProducto btn btn-warning" onclick="procesoProductos('2')">Actualizar datos</button>
                <button type="button" class="modalGuardar btn btn-primary" id="guardarProductoBtn" onclick="procesoProductos('1')">Guardar registro</button>
            </div>
        </div>
    </div>
</div>


<script src="<?= base_url('public/js/productos.js') ?>"></script>