<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Mnt Colonias</h1>
    <p class="mb-4">Agregar colonias asociadas a un distrito</p>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Seleccione un Departamento</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-sm">
                    <div class="form-row">
                        <div class="form-group col-sm">
                            <label for="deptoClienteCN">Departamento</label>
                            <select id="deptoClienteCN" class="form-control">
                                <option selected>Seleccione...</option>
                            </select>
                        </div>
                        <div class="form-group col-sm">
                            <label for="muniClienteCN">Municipio</label>
                            <select id="muniClienteCN" class="form-control">
                                <option value="-1" selected>Seleccione...</option>
                            </select>
                        </div>
                        <div class="form-group col-sm">
                            <label for="distritoClienteCN">Distrito</label>
                            <select id="distritoClienteCN" class="form-control" data-distrito-seleccionado="-1">
                                <option value="-1" selected>Seleccione...</option>
                            </select>
                        </div>
                        <div class="form-group col-sm">
                            <label for="coloniasCN">Colonias</label>
                            <select id="coloniasCN" class="form-control">
                                <option value="-1" selected>Seleccione...</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" id="optionNewColonia" id="optionNewColonia" style="display: none;">
                <div class="col-sm-6">
                    <div class="row">
                        <div class="col-sm">
                            <div class="form-group">
                                <label for="textColonia">Colonia</label>
                                <input type="text" class="form-control" id="textColonia">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <button type="button" class="btn btn-primary" onclick="agregarColonia()">Agregar colonia</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?= base_url('public/js/colonias.js') ?>"></script>