<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Mantenimiento de sucursales</h1>
    <p class="mb-4">Este mantenimiento te permite crear nuevas sucursales</p>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <button type="button" class="btn btn-primary" id="nuevaFechaBtn" data-toggle="modal" data-target="#nuevaSucursal">
                Agregar sucursal
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTableSuc" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Sucursal</th>
                            <th>Departamento</th>
                            <th>Municipio</th>
                            <th>Distrito</th>
                            <th>Colonia</th>
                            <th>Opciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal para nueva fecha -->
<div class="modal fade" id="nuevaSucursal" tabindex="-1" role="dialog" aria-labelledby="nuevaSucursalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <!-- <form id="formNuevaFecha"> -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar sucursal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="form-group col-md">
                    <label for="sucursal">Sucursal</label>
                    <input type="text" class="form-control" id="sucursal" name="sucursal" required>
                    <input type="hidden" class="form-control" id="idsucursal" name="idsucursal">
                </div>

                <div class="form-group col-md">
                    <label for="deptoSuc">Departamento</label>
                    <select id="deptoSuc" class="form-control">
                        <option value="-1" selected>Seleccione...</option>
                    </select>
                </div>
                <div class="form-group col-md">
                    <label for="muniSuc">Municipio</label>
                    <select id="muniSuc" class="form-control">
                        <option value="-1" selected>Seleccione...</option>
                    </select>
                </div>
                <div class="form-group col-md">
                    <label for="distritoSuc">Distrito</label>
                    <select id="distritoSuc" class="form-control">
                        <option value="-1" selected>Seleccione...</option>
                    </select>
                </div>

                <div class="form-group col-md">
                    <label for="coloniaSuc">Colonia</label>
                    <select id="coloniaSuc" class="form-control">
                        <option value="-1" selected>Seleccione...</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" id="btnGuardarSuc">Guardar</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
        <!-- </form> -->
    </div>
</div>

<script src="<?= base_url('public/js/sucursales.js') ?>"></script>