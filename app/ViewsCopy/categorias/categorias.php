<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Productos</h1>
    <p class="mb-4">Se visualizaran todos los productos agregados.</p>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <button type="button" class="btn btn-primary" id="nuevaCategoriaBtn" data-toggle="modal" data-target="#nuevaCategoria">
                Agregar categoria
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTableCategorias" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Categoria</th>
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

<div class="modal fade" id="nuevaCategoria" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="nuevaCategoriaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="nuevaCategoriaLabel">Categoria</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <input type="text" class="form-control" id="id_categoria" hidden>
                <div class="Categorias">
                    <div class="form-group row">
                        <div class="col-sm mb-3 mb-sm-0">
                            <label for="nombreCategoria">Nombre:</label>
                            <input type="text" class="form-control" id="nombreCategoria">
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
                            Si el estado es inactivo, esta categoria no aparecera en la lista de seleccion para los productos
                        </div>
                    </div>
                </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="modalEditarCategoria btn btn-warning" onclick="procesoCategoria('2')">Actualizar datos</button>
                <button type="button" class="modalGuardar btn btn-primary" onclick="procesoCategoria('1')">Guardar registro</button>
            </div>
        </div>
    </div>
</div>
<script src="<?= base_url('public/js/categorias.js') ?>"></script>