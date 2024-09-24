<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Nuevo cliente</h1>
    <p class="mb-4">Ingrese los datos del nuevo cliente.</p>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="nombrePersonalCN">Nombre completo</label>
                    <input type="text" class="form-control" id="nombrePersonalCN">
                </div>
                <div class="form-group col-md-3">
                    <label for="duiPersonal">DUI</label>
                    <input type="text" class="form-control duiG" id="duiPersonal">
                </div>
                <div class="form-group col-md-3">
                    <label for="fechaNacimientoCN">Fecha nacimientos</label>
                    <input type="date" class="form-control" id="fechaNacimientoCN">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="direccionActualCN">Dirección actual</label>
                    <input type="text" class="form-control" id="direccionActualCN">
                </div>
                <div class="form-group col-md-3">
                    <label for="deptoClienteCN">Departamento</label>
                    <select id="deptoClienteCN" class="form-control">
                        <option selected>Seleccione...</option>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="muniClienteCN">Municipio</label>
                    <select id="muniClienteCN" class="form-control">
                        <option selected>Seleccione...</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-2">
                    <label for="telPersonal">Tel. personal</label>
                    <input type="text" class="form-control telG" id="telPersonal">
                </div>
                <div class="form-group col-md-3">
                    <label for="CpropiaCN">Vive en casa propia</label>
                    <select id="CpropiaCN" class="form-control">
                        <option value="-1" selected>Seleccione...</option>
                        <option value="SI">SI</option>
                        <option value="NO">NO</option>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="CpromesaVentaCN">o en promesa de venta</label>
                    <select id="CpromesaVentaCN" class="form-control">
                        <option value="-1" selected>Seleccione...</option>
                        <option value="SI">SI</option>
                        <option value="NO">NO</option>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="CalquiladaCN">Alquilada</label>
                    <select id="CalquiladaCN" class="form-control">
                        <option value="-1" selected>Seleccione...</option>
                        <option value="SI">SI</option>
                        <option value="NO">NO</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-5">
                    <label for="aQuienPerteneceCN">A quien</label>
                    <input type="text" class="form-control" id="aQuienPerteneceCN">
                </div>
                <div class="form-group col-md-3">
                    <label for="telPropietarioCN">Tel. del propietario</label>
                    <input type="text" class="form-control" id="telPropietarioCN">
                </div>
                <div class="form-group col-md-4">
                    <label for="tiempoDeVivirDomicilio">Tiempo de vivir en el domicilio</label>
                    <input type="text" class="form-control" id="tiempoDeVivirDomicilioCN">
                </div>
            </div>
            <div class="form-row">

                <div class="form-group col-md-3">
                    <label for="estadoCivilCN">Estado Civil</label>
                    <select id="estadoCivilCN" class="form-control">
                        <option value="-1" selected>Seleccione...</option>
                        <option value="Soltera/o">Soltera/o</option>
                        <option value="Casada/o">Casada/o</option>
                        <option value="Acompañada/o">Acompañada/o</option>
                        <option value="Viuda/o">Viuda/o</option>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="correoCN">Correo Electronico</label>
                    <input type="text" class="form-control" id="correoCN">
                </div>
                <div class="form-group col-md-5">
                    <label for="nombreConyugueCN">Nombre del cónyugue</label>
                    <input type="text" class="form-control" id="nombreConyugueCN">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-10">
                    <label for="dirTrabajoConyugueCN">Lugar y direccion de trabajo del conyugue</label>
                    <input type="text" class="form-control" id="dirTrabajoConyugueCN">
                </div>
                <div class="form-group col-md-2">
                    <label for="telTrabajoConyugueCN">Tel. trabajo del conyugue</label>
                    <input type="text" class="form-control telG" id="telTrabajoConyugueCN">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="nombresPadresCN">Nombre del Padre o Madre</label>
                    <input type="text" class="form-control" id="nombresPadresCN">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-10">
                    <label for="direccionDeLosPadresCN">Direccion de los padres</label>
                    <input type="text" class="form-control" id="direccionDeLosPadresCN">
                </div>
                <div class="form-group col-md-2">
                    <label for="telPadresCN">Tel. de los padres</label>
                    <input type="text" class="form-control telG" id="telPadresCN">
                </div>
            </div>
            <div class="form-row justify-content-center align-items-center">
                <div class="alert alert-danger d-none" role="alert">
                    Algunos valores estan vacios, los campos marcados en rojo son requeridos.
                </div>
            </div>
            <div class="form-row justify-content-center align-items-center">
                <div class="form-group col-md-5">
                    <button type="button" class="btn btn-primary btn-block" id="agregarCN" onclick="validarDatos()"><B>GUARDAR DATOS</B></button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('public/js/nuevo_cliente.js') ?>"></script>