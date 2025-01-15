<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800"><?= !empty($datosClientes) && is_array($datosClientes) ? "Editar cliente" : "Nuevo cliente" ?></h1>
    <p class="mb-4"><?= !empty($datosClientes) && is_array($datosClientes) ? "Actualizar los datos del cliente" : "Ingrese los datos del nuevo cliente." ?></p>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <input type="text" class="form-control" id="idPersonaEditar" hidden value="<?= isset($datosClientes) && is_array($datosClientes) ? $datosClientes['id_cliente'] : "" ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="nombrePersonalCN">Nombre completo</label>
                    <input type="text" class="form-control" id="nombrePersonalCN" value="<?= isset($datosClientes) && is_array($datosClientes) ? $datosClientes['nombre_completo'] : "" ?>">
                </div>
                <div class="form-group col-md-3">
                    <label for="duiPersonal">DUI</label>
                    <input type="text" class="form-control duiG" id="duiPersonal" value="<?= isset($datosClientes) && is_array($datosClientes) ? $datosClientes['dui'] : "" ?>">
                </div>
                <div class="form-group col-md-3">
                    <label for="fechaNacimientoCN">Fecha nacimiento</label>
                    <input type="date" class="form-control" id="fechaNacimientoCN" value="<?= isset($datosClientes) && is_array($datosClientes) ? $datosClientes['fecha_nacimiento'] : "" ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="direccionActualCN">Dirección actual</label>
                    <input type="text" class="form-control" id="direccionActualCN" value="<?= isset($datosClientes) && is_array($datosClientes) ? $datosClientes['direccion'] : "" ?>">
                </div>
                <div class="form-group col-md-2">
                    <label for="deptoClienteCN">Departamento</label>
                    <select id="deptoClienteCN" class="form-control" data-depto-seleccionado="<?= isset($datosClientes) && is_array($datosClientes) ? $datosClientes['departamento'] : -1 ?>">
                        <option value="-1" selected>Seleccione...</option>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label for="muniClienteCN">Municipio</label>
                    <select id="muniClienteCN" class="form-control" data-muni-seleccionado="<?= isset($datosClientes) && is_array($datosClientes) ? $datosClientes['municipio'] : -1 ?>">
                        <option value="-1" selected>Seleccione...</option>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label for="distritoClienteCN">Distrito</label>
                    <select id="distritoClienteCN" class="form-control" data-distrito-seleccionado="<?= isset($datosClientes) && is_array($datosClientes) ? $datosClientes['distrito'] : -1 ?>">
                        <option value="-1" selected>Seleccione...</option>
                    </select>
                </div>

                <div class="form-group col-md-2">
                    <label for="coloniaClienteCN">Colonia</label>
                    <select id="coloniaClienteCN" class="form-control" data-colonia-seleccionado="<?= isset($datosClientes) && is_array($datosClientes) ? $datosClientes['colonia'] : -1 ?>">
                    <option value="-1" selected>Seleccione...</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="telPersonal">Tel. personal</label>
                    <input type="text" class="form-control telG" id="telPersonal" value="<?= isset($datosClientes) && is_array($datosClientes) ? $datosClientes['telefono'] : "" ?>">
                </div>
                <div class="form-group col-md-3">
                    <label for="CpropiaCN">Vive en casa propia</label>
                    <select id="CpropiaCN" class="form-control">
                        <option value="-1" <?= isset($datosClientes) && is_array($datosClientes) && $datosClientes['CpropiaCN'] == "-1" ? 'selected' : "" ?>>Seleccione...</option>
                        <option value="SI" <?= isset($datosClientes) && is_array($datosClientes) && $datosClientes['CpropiaCN'] == "SI" ? 'selected' : "" ?>>SI</option>
                        <option value="NO" <?= isset($datosClientes) && is_array($datosClientes) && $datosClientes['CpropiaCN'] == "NO" ? 'selected' : "" ?>>NO</option>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="CpromesaVentaCN">o en promesa de venta</label>
                    <select id="CpromesaVentaCN" class="form-control">
                        <option value="-1" <?= isset($datosClientes) && is_array($datosClientes) && $datosClientes['CpromesaVentaCN'] == "-1" ? 'selected' : "" ?>>Seleccione...</option>
                        <option value="SI" <?= isset($datosClientes) && is_array($datosClientes) && $datosClientes['CpromesaVentaCN'] == "SI" ? 'selected' : "" ?>>SI</option>
                        <option value="NO" <?= isset($datosClientes) && is_array($datosClientes) && $datosClientes['CpromesaVentaCN'] == "NO" ? 'selected' : "" ?>>NO</option>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="CalquiladaCN">Alquilada</label>
                    <select id="CalquiladaCN" class="form-control">
                        <option value="-1" <?= isset($datosClientes) && is_array($datosClientes) && $datosClientes['CalquiladaCN'] == "-1" ? 'selected' : "" ?>>Seleccione...</option>
                        <option value="SI" <?= isset($datosClientes) && is_array($datosClientes) && $datosClientes['CalquiladaCN'] == "SI" ? 'selected' : "" ?>>SI</option>
                        <option value="NO" <?= isset($datosClientes) && is_array($datosClientes) && $datosClientes['CalquiladaCN'] == "NO" ? 'selected' : "" ?>>NO</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-5">
                    <label for="aQuienPerteneceCN">A quien</label>
                    <input type="text" class="form-control" id="aQuienPerteneceCN" value="<?= isset($datosClientes) && is_array($datosClientes) ? $datosClientes['aQuienPerteneceCN'] : "" ?>">
                </div>
                <div class="form-group col-md-3">
                    <label for="telPropietarioCN">Tel. del propietario</label>
                    <input type="text" class="form-control" id="telPropietarioCN" value="<?= isset($datosClientes) && is_array($datosClientes) ? $datosClientes['telPropietarioCN'] : "" ?>">
                </div>
                <div class="form-group col-md-4">
                    <label for="tiempoDeVivirDomicilio">Tiempo de vivir en el domicilio</label>
                    <input type="text" class="form-control" id="tiempoDeVivirDomicilioCN" value="<?= isset($datosClientes) && is_array($datosClientes) ? $datosClientes['tiempoDeVivirDomicilioCN'] : "" ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="estadoCivilCN">Estado Civil</label>
                    <select id="estadoCivilCN" class="form-control">
                        <option value="-1" <?= isset($datosClientes) && is_array($datosClientes) && $datosClientes['estado_civil'] == "-1" ? 'selected' : "" ?>>Seleccione...</option>
                        <option value="Soltera/o" <?= isset($datosClientes) && is_array($datosClientes) && $datosClientes['estado_civil'] == "Soltera/o" ? 'selected' : "" ?>>Soltera/o</option>
                        <option value="Casada/o" <?= isset($datosClientes) && is_array($datosClientes) && $datosClientes['estado_civil'] == "Casada/o" ? 'selected' : "" ?>>Casada/o</option>
                        <option value="Acompañada/o" <?= isset($datosClientes) && is_array($datosClientes) && $datosClientes['estado_civil'] == "Acompañada/o" ? 'selected' : "" ?>>Acompañada/o</option>
                        <option value="Viuda/o" <?= isset($datosClientes) && is_array($datosClientes) && $datosClientes['estado_civil'] == "Viuda/o" ? 'selected' : "" ?>>Viuda/o</option>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="correoCN">Correo Electronico</label>
                    <input type="text" class="form-control" id="correoCN" value="<?= isset($datosClientes) && is_array($datosClientes) ? $datosClientes['correo'] : "" ?>">
                </div>
                <div class="form-group col-md-5">
                    <label for="nombreConyugueCN">Nombre del cónyugue</label>
                    <input type="text" class="form-control" id="nombreConyugueCN" value="<?= isset($datosClientes) && is_array($datosClientes) ? $datosClientes['nombre_conyugue'] : "" ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-10">
                    <label for="dirTrabajoConyugueCN">Lugar y direccion de trabajo del conyugue</label>
                    <input type="text" class="form-control" id="dirTrabajoConyugueCN" value="<?= isset($datosClientes) && is_array($datosClientes) ? $datosClientes['direccion_trabajo_conyugue'] : "" ?>">
                </div>
                <div class="form-group col-md-2">
                    <label for="telTrabajoConyugueCN">Tel. trabajo conyugue</label>
                    <input type="text" class="form-control" id="telTrabajoConyugueCN" value="<?= isset($datosClientes) && is_array($datosClientes) ? $datosClientes['telefono_trabajo_conyugue'] : "" ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="nombresPadresCN">Nombre del Padre o Madre</label>
                    <input type="text" class="form-control" id="nombresPadresCN" value="<?= isset($datosClientes) && is_array($datosClientes) ? $datosClientes['nombre_padres'] : "" ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-10">
                    <label for="direccionDeLosPadresCN">Direccion de los padres</label>
                    <input type="text" class="form-control" id="direccionDeLosPadresCN" value="<?= isset($datosClientes) && is_array($datosClientes) ? $datosClientes['direccion_padres'] : "" ?>">
                </div>
                <div class="form-group col-md-2">
                    <label for="telPadresCN">Tel. de los padres</label>
                    <input type="text" class="form-control telG" id="telPadresCN" value="<?= isset($datosClientes) && is_array($datosClientes) ? $datosClientes['telefono_padres'] : "" ?>">
                </div>
            </div>
            <div class="form-row justify-content-center align-items-center">
                <div class="alert alert-danger d-none" role="alert">
                    Algunos valores estan vacios, los campos marcados en rojo son requeridos.
                </div>
            </div>
            <div class="form-row justify-content-center align-items-center">
                <div class="form-group col-md-5">
                    <button type="button" class="btn btn-primary btn-block" id="agregarCN" onclick="validarDatos()">
                        <B><?= !empty($datosClientes) && is_array($datosClientes) ? "ACTUALIZAR DATOS" : "GUARDAR DATOS" ?></B>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('public/js/nuevo_cliente.js') ?>"></script>