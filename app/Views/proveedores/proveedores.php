<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Productos</h1>
    <p class="mb-4">Se visualizarán todos los productos agregados.</p>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <button type="button" class="btn btn-primary" id="nuevoProveedorBtn" data-toggle="modal" data-target="#nuevoProveedorModal">
                Agregar proveedor
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTableProveedores" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Contacto</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Dirección</th>
                            <th>Estado</th>
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

<div class="modal fade" id="nuevoProveedorModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="nuevoProveedorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="nuevoProveedorModalLabel">Nuevo proveedor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="text" class="form-control" id="id_proveedor" hidden>
                <div class="form-group row">
                    <div class="col-sm-6 mb-3 mb-sm-0">
                        <label for="nombre">Nombre:</label>
                        <input type="text" class="form-control" id="nombre" placeholder="Ingrese el nombre">
                    </div>
                    <div class="col-sm-6">
                        <label for="contacto">Contacto:</label>
                        <input type="text" class="form-control" id="contacto" placeholder="Ingrese el contacto">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-6 mb-3 mb-sm-0">
                        <label for="telefono">Teléfono:</label>
                        <input type="text" class="form-control" id="telefono" placeholder="Ingrese el teléfono">
                    </div>
                    <div class="col-sm-6">
                        <label for="email">Email:</label>
                        <input type="email" class="form-control" id="email" placeholder="Ingrese el email">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm mb-3 mb-sm-0">
                        <label for="direccion">Dirección:</label>
                        <input type="text" class="form-control" id="direccion" placeholder="Ingrese la dirección">
                    </div>
                </div>
                <div class="form-group row estado">
                    <div class="col-sm-5 mb-3 mb-sm-0">
                        <label for="estado">Estado:</label>
                        <select class="custom-select" id="estado">
                            <option value="-1" selected>Seleccione</option>
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row estado">
                    <div class="col-sm-12 mbt-4">
                        <div class="alert alert-warning alertEstadoInactivo" role="alert" style="display: none;">
                            Si el estado es inactivo, este proveedor no aparecera en la lista de seleccion para los productos
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cerrarModalProveedor">Cerrar</button>
                <button type="button" class="modalEditarProveedor btn btn-warning" onclick="procesoProveedor('2')">Actualizar datos</button>
                <button type="button" class="modalGuardar btn btn-primary" onclick="procesoProveedor('1')">Guardar registro</button>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('public/js/proveedor.js') ?>"></script>