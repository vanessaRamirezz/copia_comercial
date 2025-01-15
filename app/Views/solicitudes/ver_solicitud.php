<style>
    .alert-rojo {
        background-color: #dc3545;
        /* Rojo */
        color: #fff;
        /* Texto blanco */
    }

    .alert-amarillo {
        background-color: #ffc107;
        /* Amarillo */
        color: #212529;
        /* Texto oscuro */
    }

    .alert-naranja {
        background-color: #fd7e14;
        /* Naranja */
        color: #212529;
        /* Texto oscuro */
    }

    .alert-azul {
        background-color: #007bff;
        /* Azul */
        color: #fff;
        /* Texto blanco */
    }

    .alert-verde {
        background-color: #28a745;
        /* Verde */
        color: #fff;
        /* Texto blanco */
    }
</style>
<div class="container-fluid ver_datos_solicitud">
    <h1 class="h3 mb-2 text-gray-800">Ver datos de la solicitud</h1>
    <p class="mb-4">Vista de los datos de la solicitud.</p>
    <?php if (!empty($observacionSol)) : ?>
        <?php if ($id_estado_actual == 3 || $id_estado_actual == 4) : ?>
            <div class="alert alert-danger" role="alert">
                <?= $observacionSol ?>
            </div>
        <?php elseif ($id_estado_actual == 5) : ?>
            <div class="alert alert-warning" role="alert">
                <?= $observacionSol ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    <div class="card shadow">
        <?php if (in_array($perfil, ['SUPERVISOR', 'ADMINISTRADOR', 'PROPIETARIO']) && $id_estado_actual == 1): ?>
            <div class="container mt-3">
                <div class="row text-center">

                    <div class="col-sm">
                        <button class="btn btn-success w-100" onclick="handleAction('Aprobar')">Aprobar</button>
                    </div>
                    <!-- <div class="col-sm">
                        <button class="btn btn-warning w-100" onclick="handleAction('AprobadoConObs')">Aprobado con Observaciones</button>
                    </div> -->
                    <div class="col-sm">
                        <button class="btn btn-danger w-100" onclick="handleAction('Denegada')">Denegada</button>
                    </div>
                    <div class="col-sm">
                        <button class="btn btn-secondary w-100" onclick="handleAction('Anulada')">Anulada</button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <div class="accordion" id="accordionSolicitud">
            <div class="card datos_personales">
                <div class="card-header" id="headingOne">
                    <h2 class="mb-0">
                        <button class="btn btn-block text-left" type="button" data-toggle="collapse" data-target="#datosPersonales" aria-expanded="true" aria-controls="datosPersonales">
                            I. DATOS PERSONALES
                        </button>
                    </h2>
                </div>

                <input type="text" class="form-control" id="id_cliente" name="id_cliente" value="<?= $cliente['id_cliente']; ?>" disabled hidden>
                <div id="datosPersonales" class="collapse show" aria-labelledby="headingOne" data-parent="#datosPersonales">
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-sm d-flex">
                                <div class="form-group">
                                    <label for="creacionDocumento">Fecha creación</label>
                                    <input type="date" class="form-control" id="creacionDocumento" value="<?= date('Y-m-d', strtotime($cliente['fechaCreacion'])); ?>" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="nombrePersonal">Nombre</label>
                                <input type="text" class="form-control" id="nombrePersonal" value="<?= $cliente['nombre_completo']; ?>" disabled>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="duiPersonal">DUI</label>
                                <input type="text" class="form-control duiG" id="duiPersonal" value="<?= $cliente['dui']; ?>" disabled>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="fechaNacimiento">Fecha nacimiento</label>
                                <input type="date" class="form-control" id="fechaNacimiento" value="<?= date('Y-m-d', strtotime($cliente['fecha_nacimiento'])); ?>" disabled>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="direccionActual">Dirección actual</label>
                                <input type="text" class="form-control" id="direccionActual" value="<?= $cliente['direccion']; ?>" disabled>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="deptoCliente">Departamento</label>
                                <select id="deptoCliente" class="form-control" disabled>
                                    <option selected>Seleccione...</option>
                                    <!-- Selección dinámica -->
                                    <option value="<?= $cliente['departamento']; ?>" selected><?= $deptClienteN; ?></option>
                                    <!-- Agregar más opciones según tu lista de departamentos -->
                                </select>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="muniCliente">Municipio</label>
                                <select id="muniCliente" class="form-control" disabled>
                                    <option selected>Seleccione...</option>
                                    <!-- Selección dinámica -->
                                    <option value="<?= $cliente['municipio']; ?>" selected><?= $muniClienteN; ?></option>
                                    <!-- Agregar más opciones según tu lista de municipios -->
                                </select>
                            </div>

                            <div class="form-group col-md-2">
                                <label for="coloniaCliente">Colonia</label>
                                <select id="coloniaCliente" class="form-control" disabled>
                                    <option selected>Seleccione...</option>
                                    <option value="<?= $cliente['colonia']; ?>" selected><?= $coloniaCliente; ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label for="telefono">Teléfono</label>
                                <input type="text" class="form-control telG" id="telefono" value="<?= $cliente['telefono']; ?>" disabled>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="Cpropia">Vive en casa propia</label>
                                <select id="Cpropia" class="form-control" disabled>
                                    <option value="-1" <?= $cliente['CpropiaCN'] == '' ? 'selected' : ''; ?>>Seleccione...</option>
                                    <option value="SI" <?= $cliente['CpropiaCN'] == 'SI' ? 'selected' : ''; ?>>SI</option>
                                    <option value="NO" <?= $cliente['CpropiaCN'] == 'NO' ? 'selected' : ''; ?>>NO</option>
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="CpromesaVenta">o en promesa de venta</label>
                                <select id="CpromesaVenta" class="form-control" disabled>
                                    <option value="-1" <?= $cliente['CpromesaVentaCN'] == '' ? 'selected' : ''; ?>>Seleccione...</option>
                                    <option value="SI" <?= $cliente['CpromesaVentaCN'] == 'SI' ? 'selected' : ''; ?>>SI</option>
                                    <option value="NO" <?= $cliente['CpromesaVentaCN'] == 'NO' ? 'selected' : ''; ?>>NO</option>
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="Calquilada">Alquilada</label>
                                <select id="Calquilada" class="form-control" disabled>
                                    <option value="-1" <?= $cliente['CalquiladaCN'] == '' ? 'selected' : ''; ?>>Seleccione...</option>
                                    <option value="SI" <?= $cliente['CalquiladaCN'] == 'SI' ? 'selected' : ''; ?>>SI</option>
                                    <option value="NO" <?= $cliente['CalquiladaCN'] == 'NO' ? 'selected' : ''; ?>>NO</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-5">
                                <label for="aQuienPertenece">A quien</label>
                                <input type="text" class="form-control" id="aQuienPertenece" value="<?= $cliente['aQuienPerteneceCN']; ?>" disabled>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="telPropietario">Tel. del propietario</label>
                                <input type="text" class="form-control telG" id="telPropietario" value="<?= $cliente['telPropietarioCN']; ?>" disabled>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="tiempoDeVivirDomicilio">Tiempo de vivir en el domicilio</label>
                                <input type="text" class="form-control" id="tiempoDeVivirDomicilio" value="<?= $cliente['tiempoDeVivirDomicilioCN']; ?>" disabled>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="direccionAnterior">Dirección anterior (Si la actual es menor a dos años)</label>
                            <input type="text" class="form-control" id="direccionAnterior" value="<?= $cliente['direccion_anterior'] ?? ''; ?>" disabled>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="estadoCivil">Estado Civil</label>
                                <select id="estadoCivil" class="form-control" disabled>
                                    <option value="Soltera/o" <?= $cliente['estado_civil'] == 'Soltera/o' ? 'selected' : ''; ?>>Soltera/o</option>
                                    <option value="Casada/o" <?= $cliente['estado_civil'] == 'Casada/o' ? 'selected' : ''; ?>>Casada/o</option>
                                    <option value="Acompañada/o" <?= $cliente['estado_civil'] == 'Acompañada/o' ? 'selected' : ''; ?>>Acompañada/o</option>
                                    <option value="Viuda/o" <?= $cliente['estado_civil'] == 'Viuda/o' ? 'selected' : ''; ?>>Viuda/o</option>
                                </select>
                            </div>
                            <div class="form-group col-md-8">
                                <label for="nombreConyugue">Nombre del cónyuge</label>
                                <input type="text" class="form-control" id="nombreConyugue" value="<?= $cliente['nombre_conyugue']; ?>" disabled>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-9">
                                <label for="dirTrabajoConyugue">Lugar y direccion de trabajo del cónyuge</label>
                                <input type="text" class="form-control" id="dirTrabajoConyugue" value="<?= $cliente['direccion_trabajo_conyugue']; ?>" disabled>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="telTrabajoConyugue">Tel. trabajo del cónyuge</label>
                                <input type="text" class="form-control telG" id="telTrabajoConyugue" value="<?= $cliente['telefono_trabajo_conyugue']; ?>" disabled>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm">
                                <button class="btn btn-primary float-right next-btn" type="button">Siguiente</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card referencias_laborales">
                <div class="card-header" id="headingTwo">
                    <h2 class="mb-0">
                        <button class="btn btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#refLaborales" aria-expanded="false" aria-controls="refLaborales">
                            II. REFERENCIAS LABORALES
                        </button>
                    </h2>
                </div>
                <div id="refLaborales" class="collapse" aria-labelledby="headingTwo" data-parent="#refLaborales">
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="profesionOficio">Profesion u Oficio</label>
                                <input type="text" class="form-control" id="profesionOficio" name="profesion_oficio" value="<?php echo htmlspecialchars($refLaboral[0]['descripcion']); ?>">
                            </div>
                            <div class="form-group col-md-8">
                                <label for="lugarTrabajo">Patron/Empresa/Lugar de trabajo</label>
                                <input type="text" class="form-control" id="lugarTrabajo" name="empresa" value="<?php echo htmlspecialchars($refLaboral[0]['empresa']); ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="direccionDeTrabajo">Direccion del trabajo</label>
                                <input type="text" class="form-control" id="direccionDeTrabajo" name="direccion_trabajo" value="<?php echo htmlspecialchars($refLaboral[0]['direccion_trabajo']); ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label for="telTrabajo">Telefono del trabajo</label>
                                <input type="text" class="form-control telG" id="telTrabajo" name="telefono_trabajo" value="<?php echo htmlspecialchars($refLaboral[0]['telefono_trabajo']); ?>">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="cargoDesempeña">Cargo que desempeña</label>
                                <input type="text" class="form-control" id="cargoDesempeña" name="cargo" value="<?php echo htmlspecialchars($refLaboral[0]['cargo']); ?>">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="salario">Salario</label>
                                <input type="text" class="form-control montosG" id="salario" name="salario" value="<?php echo htmlspecialchars($refLaboral[0]['salario']); ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="tiempoDeLaborar">Tiempo de laborar en la empresa</label>
                                <input type="text" class="form-control" id="tiempoDeLaborar" name="tiempo_laborando_empresa" value="<?php echo htmlspecialchars($refLaboral[0]['tiempo_laborado_empresa']); ?>">
                            </div>
                            <div class="form-group col-md-8">
                                <label for="nombreJefe">Nombre del jefe inmediato</label>
                                <input type="text" class="form-control" id="nombreJefe" name="nombre_jefe_inmediato" value="<?php echo htmlspecialchars($refLaboral[0]['nombre_jefe_inmediato']); ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-9">
                                <label for="empresaAnterior">Empresa en que laboro anteriormente</label>
                                <input type="text" class="form-control" id="empresaAnterior" name="empresa_anterior" value="<?php echo htmlspecialchars($refLaboral[0]['empresa_anterior']); ?>">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="telEmpresaAnterior">Tel. de la empresa anterior</label>
                                <input type="text" class="form-control telG" id="telEmpresaAnterior" name="telefono_empresa_anterior" value="<?php echo htmlspecialchars($refLaboral[0]['telefono_empresa_anterior']); ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm">
                                <button class="btn btn-primary float-right next-btn" type="button">Siguiente</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card referencias_familiares">
                <div class="card-header" id="headingThree">
                    <h2 class="mb-0">
                        <button class="btn btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#refFamiliares" aria-expanded="false" aria-controls="refFamiliares">
                            III. REFERENCIAS FAMILIARES (Parientes que no vivan en la misma dirección)
                        </button>
                    </h2>
                </div>
                <div id="refFamiliares" class="collapse" aria-labelledby="headingThree" data-parent="#refFamiliares">
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-5">
                                <label for="nombreRefFamiliarUno">1. Nombre</label>
                                <input type="text" class="form-control" id="nombreRefFamiliarUno" name="nombre" value="<?= isset($refFamiliares[0]) ? esc($refFamiliares[0]['nombre']) : '' ?>">
                            </div>
                            <div class="form-group col-md-2">
                                <label for="parentescoRefFamiliarUno">Parentesco</label>
                                <select id="parentescoRefFamiliarUno" class="form-control" name="parentesco">
                                    <option value="PAPÁ" <?= isset($refFamiliares[0]) && $refFamiliares[0]['parentesco'] === 'PAPÁ' ? 'selected' : '' ?>>PAPÁ</option>
                                    <option value="MAMÁ" <?= isset($refFamiliares[0]) && $refFamiliares[0]['parentesco'] === 'MAMÁ' ? 'selected' : '' ?>>MAMÁ</option>
                                    <option value="HERMANO/A" <?= isset($refFamiliares[0]) && $refFamiliares[0]['parentesco'] === 'HERMANO/A' ? 'selected' : '' ?>>HERMANO/A</option>
                                    <option value="TIO/A" <?= isset($refFamiliares[0]) && $refFamiliares[0]['parentesco'] === 'TIO/A' ? 'selected' : '' ?>>TIO/A</option>
                                    <option value="PRIMO/A" <?= isset($refFamiliares[0]) && $refFamiliares[0]['parentesco'] === 'PRIMO/A' ? 'selected' : '' ?>>PRIMO/A</option>
                                    <option value="ABUELO/A" <?= isset($refFamiliares[0]) && $refFamiliares[0]['parentesco'] === 'ABUELO/A' ? 'selected' : '' ?>>ABUELO/A</option>
                                </select>
                            </div>
                            <div class="form-group col-md-5">
                                <label for="dirRefFamiliarUno">Dirección</label>
                                <input type="text" class="form-control" id="dirRefFamiliarUno" name="direccion" value="<?= isset($refFamiliares[0]) ? esc($refFamiliares[0]['direccion']) : '' ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label for="telProRefFamiliarUno">Teléfono</label>
                                <input type="text" class="form-control telG" id="telProRefFamiliarUno" name="telefono" value="<?= isset($refFamiliares[0]) ? esc($refFamiliares[0]['telefono']) : '' ?>">
                            </div>
                            <div class="form-group col-md-5">
                                <label for="lugarTrabajoRefFamiliarUno">Lugar de trabajo</label>
                                <input type="text" class="form-control" id="lugarTrabajoRefFamiliarUno" name="lugar_trabajo" value="<?= isset($refFamiliares[0]) ? esc($refFamiliares[0]['lugar_trabajo']) : '' ?>">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="telTrabaRefFamiliarUno">Teléfono trabajo</label>
                                <input type="text" class="form-control telG" id="telTrabaRefFamiliarUno" name="telefono_trabajo" value="<?= isset($refFamiliares[0]) ? esc($refFamiliares[0]['telefono_trabajo']) : '' ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-5">
                                <label for="nombreRefFamiliarDos">2. Nombre</label>
                                <input type="text" class="form-control" id="nombreRefFamiliarDos" name="nombre" value="<?= isset($refFamiliares[1]) ? esc($refFamiliares[1]['nombre']) : '' ?>">
                            </div>
                            <div class="form-group col-md-2">
                                <label for="parentescoRefFamiliarDos">Parentesco</label>
                                <select id="parentescoRefFamiliarDos" class="form-control" name="parentesco">
                                    <option value="PAPÁ" <?= isset($refFamiliares[1]) && $refFamiliares[1]['parentesco'] === 'PAPÁ' ? 'selected' : '' ?>>PAPÁ</option>
                                    <option value="MAMÁ" <?= isset($refFamiliares[1]) && $refFamiliares[1]['parentesco'] === 'MAMÁ' ? 'selected' : '' ?>>MAMÁ</option>
                                    <option value="HERMANO/A" <?= isset($refFamiliares[1]) && $refFamiliares[1]['parentesco'] === 'HERMANO/A' ? 'selected' : '' ?>>HERMANO/A</option>
                                    <option value="TIO/A" <?= isset($refFamiliares[1]) && $refFamiliares[1]['parentesco'] === 'TIO/A' ? 'selected' : '' ?>>TIO/A</option>
                                    <option value="PRIMO/A" <?= isset($refFamiliares[1]) && $refFamiliares[1]['parentesco'] === 'PRIMO/A' ? 'selected' : '' ?>>PRIMO/A</option>
                                    <option value="ABUELO/A" <?= isset($refFamiliares[1]) && $refFamiliares[1]['parentesco'] === 'ABUELO/A' ? 'selected' : '' ?>>ABUELO/A</option>
                                </select>
                            </div>
                            <div class="form-group col-md-5">
                                <label for="dirRefFamiliarDos">Dirección</label>
                                <input type="text" class="form-control" id="dirRefFamiliarDos" name="direccion" value="<?= isset($refFamiliares[1]) ? esc($refFamiliares[1]['direccion']) : '' ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label for="telProRefFamiliarDos">Teléfono</label>
                                <input type="text" class="form-control telG" id="telProRefFamiliarDos" name="telefono" value="<?= isset($refFamiliares[1]) ? esc($refFamiliares[1]['telefono']) : '' ?>">
                            </div>
                            <div class="form-group col-md-5">
                                <label for="lugarTrabajoRefFamiliarDos">Lugar de trabajo</label>
                                <input type="text" class="form-control" id="lugarTrabajoRefFamiliarDos" name="lugar_trabajo" value="<?= isset($refFamiliares[1]) ? esc($refFamiliares[1]['lugar_trabajo']) : '' ?>">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="telTrabaRefFamiliarDos">Teléfono trabajo</label>
                                <input type="text" class="form-control telG" id="telTrabaRefFamiliarDos" name="telefono_trabajo" value="<?= isset($refFamiliares[1]) ? esc($refFamiliares[1]['telefono_trabajo']) : '' ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm">
                                <button class="btn btn-primary float-right next-btn" type="button">Siguiente</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card referencias_personas_no_familiar">
                <div class="card-header" id="headingThree">
                    <h2 class="mb-0">
                        <button class="btn btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#refNoFamiliares" aria-expanded="false" aria-controls="refNoFamiliares">
                            IV. PERSONAS NO FAMILIARES QUE PUEDAN DAR REFERENCIA
                        </button>
                    </h2>
                </div>
                <div id="refNoFamiliares" class="collapse" aria-labelledby="headingThree" data-parent="#refNoFamiliares">
                    <div class="card-body">
                        <!-- Referencia No Familiar 1 -->
                        <div class="form-row">
                            <div class="form-group col-md-5">
                                <label for="nombreRefNoFamiliarUno">1. Nombre</label>
                                <input type="text" class="form-control" id="nombreRefNoFamiliarUno" name="nombre"
                                    value="<?php echo isset($refNoFamiliares[0]['nombre']) ? htmlspecialchars($refNoFamiliares[0]['nombre']) : ''; ?>">
                            </div>
                            <div class="form-group col-md-7">
                                <label for="dirRefNoFamiliarUno">Direccion</label>
                                <input type="text" class="form-control" id="dirRefNoFamiliarUno" name="direccion"
                                    value="<?php echo isset($refNoFamiliares[0]['direccion']) ? htmlspecialchars($refNoFamiliares[0]['direccion']) : ''; ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-2">
                                <label for="telProRefNoFamiliarUno">Telefono</label>
                                <input type="text" class="form-control telG" id="telProRefNoFamiliarUno" name="telefono"
                                    value="<?php echo isset($refNoFamiliares[0]['telefono']) ? htmlspecialchars($refNoFamiliares[0]['telefono']) : ''; ?>">
                            </div>
                            <div class="form-group col-md-8">
                                <label for="lugarTrabajoRefNoFamiliarUno">Lugar de trabajo</label>
                                <input type="text" class="form-control" id="lugarTrabajoRefNoFamiliarUno" name="lugar_trabajo"
                                    value="<?php echo isset($refNoFamiliares[0]['lugar_trabajo']) ? htmlspecialchars($refNoFamiliares[0]['lugar_trabajo']) : ''; ?>">
                            </div>
                            <div class="form-group col-md-2">
                                <label for="telTrabaRefNoFamiliarUno">Telefono trabajo</label>
                                <input type="text" class="form-control telG" id="telTrabaRefNoFamiliarUno" name="telefono_trabajo"
                                    value="<?php echo isset($refNoFamiliares[0]['telefono_trabajo']) ? htmlspecialchars($refNoFamiliares[0]['telefono_trabajo']) : ''; ?>">
                            </div>
                        </div>

                        <!-- Referencia No Familiar 2 -->
                        <div class="form-row">
                            <div class="form-group col-md-5">
                                <label for="nombreRefNoFamiliarDos">2. Nombre</label>
                                <input type="text" class="form-control" id="nombreRefNoFamiliarDos" name="nombre"
                                    value="<?php echo isset($refNoFamiliares[1]['nombre']) ? htmlspecialchars($refNoFamiliares[1]['nombre']) : ''; ?>">
                            </div>
                            <div class="form-group col-md-7">
                                <label for="dirRefNoFamiliarDos">Direccion</label>
                                <input type="text" class="form-control" id="dirRefNoFamiliarDos" name="direccion"
                                    value="<?php echo isset($refNoFamiliares[1]['direccion']) ? htmlspecialchars($refNoFamiliares[1]['direccion']) : ''; ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-2">
                                <label for="telProRefNoFamiliarDos">Telefono</label>
                                <input type="text" class="form-control telG" id="telProRefNoFamiliarDos" name="telefono"
                                    value="<?php echo isset($refNoFamiliares[1]['telefono']) ? htmlspecialchars($refNoFamiliares[1]['telefono']) : ''; ?>">
                            </div>
                            <div class="form-group col-md-8">
                                <label for="lugarTrabajoRefNoFamiliarDos">Lugar de trabajo</label>
                                <input type="text" class="form-control" id="lugarTrabajoRefNoFamiliarDos" name="lugar_trabajo"
                                    value="<?php echo isset($refNoFamiliares[1]['lugar_trabajo']) ? htmlspecialchars($refNoFamiliares[1]['lugar_trabajo']) : ''; ?>">
                            </div>
                            <div class="form-group col-md-2">
                                <label for="telTrabaRefNoFamiliarDos">Telefono trabajo</label>
                                <input type="text" class="form-control telG" id="telTrabaRefNoFamiliarDos" name="telefono_trabajo"
                                    value="<?php echo isset($refNoFamiliares[1]['telefono_trabajo']) ? htmlspecialchars($refNoFamiliares[1]['telefono_trabajo']) : ''; ?>">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm">
                                <button class="btn btn-primary float-right next-btn" type="button">Siguiente</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card referencias_crediticias">
                <div class="card-header" id="headingThree">
                    <h2 class="mb-0">
                        <button class="btn btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#refCrediticiasComerciales" aria-expanded="false" aria-controls="refCrediticiasComerciales">
                            V. REFERENCIAS CREDITICIAS COMERCIALES
                        </button>
                    </h2>
                </div>
                <div id="refCrediticiasComerciales" class="collapse" aria-labelledby="headingThree" data-parent="#refCrediticiasComerciales">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>INSTITUCIÓN/CASA COMERCIAL/ALMACÉN</th>
                                            <th>TELÉFONO</th>
                                            <th>MONTO DEL CRÉDITO</th>
                                            <th>PERIODOS</th>
                                            <th>PLAZO</th>
                                            <th>ACTIVA O CANCELADA</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><input type="text" class="form-control" id="refCrediticiaNombre1" name="institucion" value="<?= isset($refCrediticia[0]['institucion']) ? htmlspecialchars($refCrediticia[0]['institucion']) : '' ?>"></td>
                                            <td><input type="text" class="form-control telG" id="refCrediticiaTel1" name="telefono" value="<?= isset($refCrediticia[0]['telefono']) ? htmlspecialchars($refCrediticia[0]['telefono']) : '' ?>"></td>
                                            <td><input type="text" class="form-control montosG" id="refCrediticiaMonto1" name="monto_credito" value="<?= isset($refCrediticia[0]['monto_credito']) ? htmlspecialchars($refCrediticia[0]['monto_credito']) : '' ?>"></td>
                                            <td><input type="text" class="form-control" id="refCrediticiaPeriodo1" name="periodos" value="<?= isset($refCrediticia[0]['periodos']) ? htmlspecialchars($refCrediticia[0]['periodos']) : '' ?>"></td>
                                            <td><input type="text" class="form-control" id="refCrediticiaPlazo1" name="plazo" value="<?= isset($refCrediticia[0]['plazo']) ? htmlspecialchars($refCrediticia[0]['plazo']) : '' ?>"></td>
                                            <td>
                                                <div class="form-group col-md">
                                                    <select id="refCrediticiaEstado1" class="form-control" name="estado">
                                                        <option value="ACTIVA" <?= (isset($refCrediticia[0]['estado']) && $refCrediticia[0]['estado'] == 'ACTIVA') ? 'selected' : '' ?>>ACTIVA</option>
                                                        <option value="CANCELADA" <?= (isset($refCrediticia[0]['estado']) && $refCrediticia[0]['estado'] == 'CANCELADA') ? 'selected' : '' ?>>CANCELADA</option>
                                                    </select>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><input type="text" class="form-control" id="refCrediticiaNombre2" name="institucion" value="<?= isset($refCrediticia[1]['institucion']) ? htmlspecialchars($refCrediticia[1]['institucion']) : '' ?>"></td>
                                            <td><input type="text" class="form-control telG" id="refCrediticiaTel2" name="telefono" value="<?= isset($refCrediticia[1]['telefono']) ? htmlspecialchars($refCrediticia[1]['telefono']) : '' ?>"></td>
                                            <td><input type="text" class="form-control montosG" id="refCrediticiaMonto2" name="monto_credito" value="<?= isset($refCrediticia[1]['monto_credito']) ? htmlspecialchars($refCrediticia[1]['monto_credito']) : '' ?>"></td>
                                            <td><input type="text" class="form-control" id="refCrediticiaPeriodo2" name="periodos" value="<?= isset($refCrediticia[1]['periodos']) ? htmlspecialchars($refCrediticia[1]['periodos']) : '' ?>"></td>
                                            <td><input type="text" class="form-control" id="refCrediticiaPlazo2" name="plazo" value="<?= isset($refCrediticia[1]['plazo']) ? htmlspecialchars($refCrediticia[1]['plazo']) : '' ?>"></td>
                                            <td>
                                                <div class="form-group col-md">
                                                    <select id="refCrediticiaEstado2" class="form-control" name="estado">
                                                        <option value="ACTIVA" <?= (isset($refCrediticia[1]['estado']) && $refCrediticia[1]['estado'] == 'ACTIVA') ? 'selected' : '' ?>>ACTIVA</option>
                                                        <option value="CANCELADA" <?= (isset($refCrediticia[1]['estado']) && $refCrediticia[1]['estado'] == 'CANCELADA') ? 'selected' : '' ?>>CANCELADA</option>
                                                    </select>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><input type="text" class="form-control" id="refCrediticiaNombre3" name="institucion" value="<?= isset($refCrediticia[2]['institucion']) ? htmlspecialchars($refCrediticia[2]['institucion']) : '' ?>"></td>
                                            <td><input type="text" class="form-control telG" id="refCrediticiaTel3" name="telefono" value="<?= isset($refCrediticia[2]['telefono']) ? htmlspecialchars($refCrediticia[2]['telefono']) : '' ?>"></td>
                                            <td><input type="text" class="form-control montosG" id="refCrediticiaMonto3" name="monto_credito" value="<?= isset($refCrediticia[2]['monto_credito']) ? htmlspecialchars($refCrediticia[2]['monto_credito']) : '' ?>"></td>
                                            <td><input type="text" class="form-control" id="refCrediticiaPeriodo3" name="periodos" value="<?= isset($refCrediticia[2]['periodos']) ? htmlspecialchars($refCrediticia[2]['periodos']) : '' ?>"></td>
                                            <td><input type="text" class="form-control" id="refCrediticiaPlazo3" name="plazo" value="<?= isset($refCrediticia[2]['plazo']) ? htmlspecialchars($refCrediticia[2]['plazo']) : '' ?>"></td>
                                            <td>
                                                <div class="form-group col-md">
                                                    <select id="refCrediticiaEstado3" class="form-control" name="estado">
                                                        <option value="ACTIVA" <?= (isset($refCrediticia[2]['estado']) && $refCrediticia[2]['estado'] == 'ACTIVA') ? 'selected' : '' ?>>ACTIVA</option>
                                                        <option value="CANCELADA" <?= (isset($refCrediticia[2]['estado']) && $refCrediticia[2]['estado'] == 'CANCELADA') ? 'selected' : '' ?>>CANCELADA</option>
                                                    </select>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm">
                                <button class="btn btn-primary float-right next-btn" type="button">Siguiente</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card analisis_socioeconomico">
                <div class="card-header" id="headingThree">
                    <h2 class="mb-0">
                        <button class="btn btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#analisisSocioEconomico" aria-expanded="false" aria-controls="analisisSocioEconomico">
                            VI. ANALISIS SOCIO/ECONOMICO
                        </button>
                    </h2>
                </div>
                <div id="analisisSocioEconomico" class="collapse" aria-labelledby="headingThree" data-parent="#analisisSocioEconomico">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12" name="socioeconomico">
                                        <table class="table custom-table mb-3">
                                            <tbody>
                                                <tr>
                                                    <td>Ingreso - Mensual</td>
                                                    <td><input type="text" class="form-control montosG ingresos" id="ingresoMensual" name="ingresoMensual" value="<?= isset($analisisSocioeconomico[0]['ingreso_mensual']) ? htmlspecialchars($analisisSocioeconomico[0]['ingreso_mensual']) : '' ?>"></td>
                                                    <td>Egreso Mensual</td>
                                                    <td><input type="text" class="form-control montosG egresos" id="egresoMensual" name="egresoMensual" value="<?= isset($analisisSocioeconomico[0]['egreso_mensual']) ? htmlspecialchars($analisisSocioeconomico[0]['egreso_mensual']) : '' ?>"></td>
                                                </tr>
                                                <tr>
                                                    <td>Salario</td>
                                                    <td><input type="text" class="form-control montosG ingresos" id="salarioIng" name="salarioIng" value="<?= isset($analisisSocioeconomico[0]['salario']) ? htmlspecialchars($analisisSocioeconomico[0]['salario']) : '' ?>"></td>
                                                    <td>Pago de Casa</td>
                                                    <td><input type="text" class="form-control montosG egresos" id="pagoCasa" name="pagoCasa" value="<?= isset($analisisSocioeconomico[0]['pago_casa']) ? htmlspecialchars($analisisSocioeconomico[0]['pago_casa']) : '' ?>"></td>
                                                </tr>
                                                <tr>
                                                    <td>Otros (Explique)</td>
                                                    <td><input type="text" class="form-control" id="otrosIngresos" name="otrosIngresos" value="<?= isset($analisisSocioeconomico[0]['otros_explicacion']) ? htmlspecialchars($analisisSocioeconomico[0]['otros_explicacion']) : '' ?>"></td>
                                                    <td>Gastos de Vida</td>
                                                    <td><input type="text" class="form-control montosG egresos" id="gastosVida" name="gastosVida" value="<?= isset($analisisSocioeconomico[0]['gastos_vida']) ? htmlspecialchars($analisisSocioeconomico[0]['gastos_vida']) : '' ?>"></td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td>Otros:</td>
                                                    <td><input type="text" class="form-control montosG egresos" id="otrosEgresos" name="otrosEgresos" value="<?= isset($analisisSocioeconomico[0]['otros']) ? htmlspecialchars($analisisSocioeconomico[0]['otros']) : '' ?>"></td>
                                                </tr>
                                                <tr>
                                                    <td>Total de Ingresos</td>
                                                    <td><input type="text" disabled class="form-control montosG" id="totalIngresos" name="totalIngresos" value="<?= isset($analisisSocioeconomico[0]['total_ingresos']) ? htmlspecialchars($analisisSocioeconomico[0]['total_ingresos']) : '' ?>"></td>
                                                    <td>Total de Egresos</td>
                                                    <td><input type="text" disabled class="form-control montosG" id="totalEgresos" name="totalEgresos" value="<?= isset($analisisSocioeconomico[0]['total_egresos']) ? htmlspecialchars($analisisSocioeconomico[0]['total_egresos']) : '' ?>"></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3">Diferencia Ingresos - Egresos:</td>
                                                    <td><input disabled type="text" class="form-control montosG" id="diferencia" name="diferencia" value="<?= isset($analisisSocioeconomico[0]['diferencia_ingresos_egresos']) ? htmlspecialchars($analisisSocioeconomico[0]['diferencia_ingresos_egresos']) : '' ?>"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-12">
                                        <h3 class="mt-4">Estado Socioeconómico</h3>
                                        <div class="progress">
                                            <div id="progress-bar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <div id="estado-label" class="mt-2"></div>
                                    </div>
                                </div>
                                <div class="row mt-5">
                                    <div class="col-md">
                                        <div class="container">
                                            <p>
                                                ADJUNTAR: Copias de DUI, de Recibo de Agua o Luz Certificado Patronal (todas recientes) y cancelaciones (si las hubiera)
                                            </p>
                                            <p>
                                                - Con el entendido que toda la información proporcionada es veraz, y autorizo a verificar el derecho de confirmarla.
                                            </p>
                                            <p>
                                                - Si el crédito no procede, la empresa retendrá el valor de los costos incurridos.
                                            </p>
                                            <p>Todo cuenta con mora paga el 3% de interes por mes de pago extemporáneo</p>
                                        </div>
                                    </div>
                                </div>
                                <!--<div class="row mt-4">
                                    <div class="col-md text-center">
                                        <b>F.___________________________</b><br>
                                        <p>FIRMA DEL CLIENTE</p>
                                    </div>
                                </div>-->
                            </div>
                            <div class="col-md-6">
                                <div class="row align-items-center justify-content-center mb-4">
                                    <div class="col-sm-8 text-left">
                                        <h6><b>ARTICULO A CONTRATAR:</b></h6>
                                    </div>
                                </div>
                                <div class="row align-items-center justify-content-center mb-4">
                                    <table class="table table-sm" id="productosSeleccionadosTbl">
                                        <thead>
                                            <tr>
                                                <th scope="col">Cod</th>
                                                <th scope="col">Producto</th>
                                                <th scope="col">Precio unidad</th>
                                                <th scope="col">cantidad</th>
                                                <th scope="col">Precio total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach ($productosSol as $producto) {
                                                $precioTotal = $producto['precio'] * $producto['cantidad_producto'];

                                                echo '<tr>';
                                                echo '<td>' . htmlspecialchars($producto['codigo_producto']) . '</td>';
                                                echo '<td>' . htmlspecialchars($producto['nombre']) . '</td>';
                                                echo '<td>' . htmlspecialchars(number_format($producto['precio'], 2)) . '</td>';
                                                echo '<td>' . htmlspecialchars($producto['cantidad_producto']) . '</td>';
                                                echo '<td>' . htmlspecialchars(number_format($precioTotal, 2)) . '</td>';
                                                echo '</tr>';
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                                <hr>
                                <div class="row mt-4" id="plan_de_pago">
                                    <div class="col-sm-12">
                                        <h6><b>PLAN DE PAGO:</b></h6>
                                    </div>
                                    <div class="col-sm">
                                        <div class="form-row">
                                            <div class="form-group col-md-12">
                                                <label for="valorArticulo">Valor del artículo:</label>
                                                <input type="text" class="form-control" id="valorArticulo" disabled value="<?php echo htmlspecialchars($planPago[0]['valor_articulo']); ?>">
                                            </div>
                                            <div class="form-group col-md-12">
                                                <label for="pagoPrima">Valor/pago de prima</label>
                                                <input type="text" class="form-control montosG" id="valorPagoPrima" value="<?php echo htmlspecialchars($planPago[0]['valor_prima']); ?>">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="saldoAPagar">Saldo a pagar</label>
                                                <input type="text" class="form-control" id="saldoAPagar" disabled value="<?php echo htmlspecialchars($planPago[0]['saldo_a_pagar']); ?>">
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-12 col-sm-6 col-md-4">
                                                <label for="cantidadCuotas">Cantidad cuotas</label>
                                                <select class="form-control" id="cantidadCuotas" name="cantidadCuotas">
                                                    <!-- Aquí puedes agregar opciones de selección si es necesario -->
                                                    <option value="<?php echo htmlspecialchars($planPago[0]['cuotas']); ?>" selected><?php echo htmlspecialchars($planPago[0]['cuotas']); ?></option>
                                                </select>
                                            </div>
                                            <div class="form-group col-12 col-sm-6 col-md-4">
                                                <label for="montoCuota">Monto cuotas de:</label>
                                                <input type="text" class="form-control" id="montoCuota" disabled value="<?php echo htmlspecialchars($planPago[0]['monto_cuotas']); ?>">
                                            </div>
                                            <div class="form-group col-12 col-sm-6 col-md-4">
                                                <label for="montoTotalPagar">Monto total a pagar:</label>
                                                <input type="text" class="form-control" id="montoTotalPagar" disabled value="<?php echo htmlspecialchars($planPago[0]['monto_total_pagar']); ?>">
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-12 col-sm">
                                                <label for="observaciones">Observación</label>
                                                <textarea class="form-control" id="observaciones" rows="4" placeholder="Ingrese su observación aquí..."><?php echo htmlspecialchars($planPago[0]['observaciones']); ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm">
                                <button class="btn btn-primary float-right next-btn" type="button">Siguiente</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card co_deudor">
                <div class="card-header" id="headingThree">
                    <h2 class="mb-0">
                        <button class="btn btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#co_deudor" aria-expanded="false" aria-controls="co_deudor">
                            VII. CO-DEUDOR(FIADOR)
                        </button>
                    </h2>
                </div>
                <div id="co_deudor" class="collapse" aria-labelledby="headingThree" data-parent="#co_deudor">
                    <div class="card-body">

                        <div class="form-row">
                            <div class="form-group col-md-10">
                                <label for="nombre">Nombre:</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($codeudor[0]['nombre']); ?>">
                            </div>
                            <div class="form-group col-md-2">
                                <label for="dui">DUI:</label>
                                <input type="text" class="form-control duiG" id="dui" name="dui" value="<?php echo htmlspecialchars($codeudor[0]['dui']); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="direccion">Dirección:</label>
                            <input type="text" class="form-control" id="direccion" name="direccion" value="<?php echo htmlspecialchars($codeudor[0]['direccion']); ?>">
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-2">
                                <label for="COtelPersonal">Tel. personal</label>
                                <input type="text" class="form-control telG" id="COtelPersonal" value="<?php echo htmlspecialchars($codeudor[0]['telefono_personal']); ?>">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="COpropiaCN">Vive en casa propia</label>
                                <select id="COpropiaCN" class="form-control">
                                    <option value="-1" <?php echo ($codeudor[0]['vive_en_casa_propia'] == '' ? 'selected' : ''); ?>>Seleccione...</option>
                                    <option value="SI" <?php echo ($codeudor[0]['vive_en_casa_propia'] == 'si' ? 'selected' : ''); ?>>SI</option>
                                    <option value="NO" <?php echo ($codeudor[0]['vive_en_casa_propia'] == 'no' ? 'selected' : ''); ?>>NO</option>
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="COpromesaVenta">o en promesa de venta</label>
                                <select id="COpromesaVenta" class="form-control">
                                    <option value="-1" <?php echo ($codeudor[0]['en_promesa_de_venta'] == '' ? 'selected' : ''); ?>>Seleccione...</option>
                                    <option value="SI" <?php echo ($codeudor[0]['en_promesa_de_venta'] == 'si' ? 'selected' : ''); ?>>SI</option>
                                    <option value="NO" <?php echo ($codeudor[0]['en_promesa_de_venta'] == 'no' ? 'selected' : ''); ?>>NO</option>
                                </select>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="COalquilada">Alquilada</label>
                                <select id="COalquilada" class="form-control">
                                    <option value="-1" <?php echo ($codeudor[0]['alquilada'] == '' ? 'selected' : ''); ?>>Seleccione...</option>
                                    <option value="SI" <?php echo ($codeudor[0]['alquilada'] == 'si' ? 'selected' : ''); ?>>SI</option>
                                    <option value="NO" <?php echo ($codeudor[0]['alquilada'] == 'no' ? 'selected' : ''); ?>>NO</option>
                                </select>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="COtiempoVivienda">Tiempo</label>
                                <input type="text" class="form-control" id="COtiempoVivienda" value="<?php echo htmlspecialchars($codeudor[0]['tiempo']); ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label for="COestadoCivil">Estado Civil</label>
                                <select id="COestadoCivil" class="form-control">
                                    <option value="-1" <?php echo ($codeudor[0]['estado_civil'] == '' ? 'selected' : ''); ?>>Seleccione...</option>
                                    <option value="Soltera/o" <?php echo ($codeudor[0]['estado_civil'] == 'Soltera/o' ? 'selected' : ''); ?>>Soltera/o</option>
                                    <option value="Casada/o" <?php echo ($codeudor[0]['estado_civil'] == 'Casada/o' ? 'selected' : ''); ?>>Casada/o</option>
                                    <option value="Acompañada/o" <?php echo ($codeudor[0]['estado_civil'] == 'Acompañada/o' ? 'selected' : ''); ?>>Acompañada/o</option>
                                    <option value="Viuda/o" <?php echo ($codeudor[0]['estado_civil'] == 'Viuda/o' ? 'selected' : ''); ?>>Viuda/o</option>
                                </select>
                            </div>
                            <div class="form-group col-md-9">
                                <label for="nombreConyugueCN">Nombre del cónyugue</label>
                                <input type="text" class="form-control" id="nombreConyugueCN" value="<?php echo htmlspecialchars($codeudor[0]['nombre_conyugue']); ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="profesion">Profesión u oficio:</label>
                                <input type="text" class="form-control" id="profesion" name="profesion" value="<?php echo htmlspecialchars($codeudor[0]['profesion_oficio']); ?>">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="patrono">Patrono/Empresa/Lugar de Trabajo:</label>
                                <input type="text" class="form-control" id="patrono" name="patrono" value="<?php echo htmlspecialchars($codeudor[0]['patrono_empresa']); ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-9">
                                <label for="COdireccionTrabajo">Dirección del trabajo:</label>
                                <input type="text" class="form-control" id="COdireccionTrabajo" name="COdireccionTrabajo" value="<?php echo htmlspecialchars($codeudor[0]['direccion_trabajo']); ?>">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="COtelefonoTrabajo">Telefono Trabajo:</label>
                                <input type="text" class="form-control telG" id="COtelefonoTrabajo" name="COtelefonoTrabajo" value="<?php echo htmlspecialchars($codeudor[0]['telefono_trabajo']); ?>">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="COcargoDesempeña">Cargo que desempeña:</label>
                                <input type="text" class="form-control" id="COcargoDesempeña" name="COcargoDesempeña" value="<?php echo htmlspecialchars($codeudor[0]['cargo']); ?>">
                            </div>
                            <div class="form-group col-md-2">
                                <label for="COsalario">Salario:</label>
                                <input type="text" class="form-control montosG" id="COsalario" name="COsalario" value="<?php echo htmlspecialchars($codeudor[0]['salario']); ?>">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="COnombreJefe">Nombre Jefe inmediato:</label>
                                <input type="text" class="form-control" id="COnombreJefe" name="COnombreJefe" value="<?php echo htmlspecialchars($codeudor[0]['nombre_jefe_inmediato']); ?>">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm">
                                <h6>Referencias</h6>
                            </div>
                        </div>
                        <div class="row" id="referenciasCodeudor">
                            <div class="col-sm-12">
                                <div class="form-row">
                                    <div class="form-group col-md-10">
                                        <label for="COnombreRef1">a) Nombre:</label>
                                        <input type="text" class="form-control coref1" id="COnombreRef1" name="COnombreRef1" value="<?php echo isset($refCodeudor[0]['nombre']) ? $refCodeudor[0]['nombre'] : ''; ?>" required>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="COparentescoRef1">Parentesco</label>
                                        <select id="COparentescoRef1" class="form-control coref1" required>
                                            <option value="PAPÁ" <?php echo (isset($refCodeudor[0]['parentesco']) && $refCodeudor[0]['parentesco'] == 'PAPÁ') ? 'selected' : ''; ?>>PAPÁ</option>
                                            <option value="MAMÁ" <?php echo (isset($refCodeudor[0]['parentesco']) && $refCodeudor[0]['parentesco'] == 'MAMÁ') ? 'selected' : ''; ?>>MAMÁ</option>
                                            <option value="HERMANO/A" <?php echo (isset($refCodeudor[0]['parentesco']) && $refCodeudor[0]['parentesco'] == 'HERMANO/A') ? 'selected' : ''; ?>>HERMANO/A</option>
                                            <option value="TIO/A" <?php echo (isset($refCodeudor[0]['parentesco']) && $refCodeudor[0]['parentesco'] == 'TIO/A') ? 'selected' : ''; ?>>TIO/A</option>
                                            <option value="PRIMO/A" <?php echo (isset($refCodeudor[0]['parentesco']) && $refCodeudor[0]['parentesco'] == 'PRIMO/A') ? 'selected' : ''; ?>>PRIMO/A</option>
                                            <option value="ABUELO/A" <?php echo (isset($refCodeudor[0]['parentesco']) && $refCodeudor[0]['parentesco'] == 'ABUELO/A') ? 'selected' : ''; ?>>ABUELO/A</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-10">
                                        <label for="COdireccionRef1">Dirección:</label>
                                        <input type="text" class="form-control coref1" id="COdireccionRef1" name="COdireccionRef1" value="<?php echo isset($refCodeudor[0]['direccion']) ? $refCodeudor[0]['direccion'] : ''; ?>" required>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="COtelRef1">Teléfono:</label>
                                        <input type="text" class="form-control coref1 telG" id="COtelRef1" name="COtelRef1" value="<?php echo isset($refCodeudor[0]['telefono']) ? $refCodeudor[0]['telefono'] : ''; ?>" pattern="^\d{8}$" title="El teléfono debe tener 8 dígitos" required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="form-row">
                                    <div class="form-group col-md-10">
                                        <label for="COnombreRef2">b) Nombre:</label>
                                        <input type="text" class="form-control coref2" id="COnombreRef2" name="COnombreRef2" value="<?php echo isset($refCodeudor[1]['nombre']) ? $refCodeudor[1]['nombre'] : ''; ?>" required>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="COparentescoRef2">Parentesco</label>
                                        <select id="COparentescoRef2" class="form-control coref2" required>
                                            <option value="PAPÁ" <?php echo (isset($refCodeudor[1]['parentesco']) && $refCodeudor[1]['parentesco'] == 'PAPÁ') ? 'selected' : ''; ?>>PAPÁ</option>
                                            <option value="MAMÁ" <?php echo (isset($refCodeudor[1]['parentesco']) && $refCodeudor[1]['parentesco'] == 'MAMÁ') ? 'selected' : ''; ?>>MAMÁ</option>
                                            <option value="HERMANO/A" <?php echo (isset($refCodeudor[1]['parentesco']) && $refCodeudor[1]['parentesco'] == 'HERMANO/A') ? 'selected' : ''; ?>>HERMANO/A</option>
                                            <option value="TIO/A" <?php echo (isset($refCodeudor[1]['parentesco']) && $refCodeudor[1]['parentesco'] == 'TIO/A') ? 'selected' : ''; ?>>TIO/A</option>
                                            <option value="PRIMO/A" <?php echo (isset($refCodeudor[1]['parentesco']) && $refCodeudor[1]['parentesco'] == 'PRIMO/A') ? 'selected' : ''; ?>>PRIMO/A</option>
                                            <option value="ABUELO/A" <?php echo (isset($refCodeudor[1]['parentesco']) && $refCodeudor[1]['parentesco'] == 'ABUELO/A') ? 'selected' : ''; ?>>ABUELO/A</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-10">
                                        <label for="COdireccionRef2">Dirección:</label>
                                        <input type="text" class="form-control coref2" id="COdireccionRef2" name="COdireccionRef2" value="<?php echo isset($refCodeudor[1]['direccion']) ? $refCodeudor[1]['direccion'] : ''; ?>" required>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="COtelRef2">Teléfono:</label>
                                        <input type="text" class="form-control coref2 telG" id="COtelRef2" name="COtelRef2" value="<?php echo isset($refCodeudor[1]['telefono']) ? $refCodeudor[1]['telefono'] : ''; ?>" pattern="^\d{8}$" title="El teléfono debe tener 8 dígitos" required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="form-row">
                                    <div class="form-group col-md-10">
                                        <label for="COnombreRef3">c) Nombre:</label>
                                        <input type="text" class="form-control coref3" id="COnombreRef3" name="COnombreRef3" value="<?php echo isset($refCodeudor[2]['nombre']) ? $refCodeudor[2]['nombre'] : ''; ?>" required>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="COparentescoRef3">Parentesco</label>
                                        <select id="COparentescoRef3" class="form-control coref3" required>
                                            <option value="PAPÁ" <?php echo (isset($refCodeudor[2]['parentesco']) && $refCodeudor[2]['parentesco'] == 'PAPÁ') ? 'selected' : ''; ?>>PAPÁ</option>
                                            <option value="MAMÁ" <?php echo (isset($refCodeudor[2]['parentesco']) && $refCodeudor[2]['parentesco'] == 'MAMÁ') ? 'selected' : ''; ?>>MAMÁ</option>
                                            <option value="HERMANO/A" <?php echo (isset($refCodeudor[2]['parentesco']) && $refCodeudor[2]['parentesco'] == 'HERMANO/A') ? 'selected' : ''; ?>>HERMANO/A</option>
                                            <option value="TIO/A" <?php echo (isset($refCodeudor[2]['parentesco']) && $refCodeudor[2]['parentesco'] == 'TIO/A') ? 'selected' : ''; ?>>TIO/A</option>
                                            <option value="PRIMO/A" <?php echo (isset($refCodeudor[2]['parentesco']) && $refCodeudor[2]['parentesco'] == 'PRIMO/A') ? 'selected' : ''; ?>>PRIMO/A</option>
                                            <option value="ABUELO/A" <?php echo (isset($refCodeudor[2]['parentesco']) && $refCodeudor[2]['parentesco'] == 'ABUELO/A') ? 'selected' : ''; ?>>ABUELO/A</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-10">
                                        <label for="COdireccionRef3">Dirección:</label>
                                        <input type="text" class="form-control coref3" id="COdireccionRef3" name="COdireccionRef3" value="<?php echo isset($refCodeudor[2]['direccion']) ? $refCodeudor[2]['direccion'] : ''; ?>" required>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="COtelRef3">Teléfono:</label>
                                        <input type="text" class="form-control coref3 telG" id="COtelRef3" name="COtelRef3" value="<?php echo isset($refCodeudor[2]['telefono']) ? $refCodeudor[2]['telefono'] : ''; ?>" pattern="^\d{8}$" title="El teléfono debe tener 8 dígitos" required>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
            <div class="container">
                <div class="row text-center">
                    <?php if (in_array($perfil, ['SUPERVISOR', 'ADMINISTRADOR', 'PROPIETARIO']) && $id_estado_actual == 1): ?>
                        <div class="col-12 col-md-6 col-lg-3 mb-3">
                            <button class="btn btn-success w-100" onclick="handleAction('Aprobar')">Aprobar</button>
                        </div>
                        <div class="col-12 col-md-6 col-lg-3 mb-3">
                            <button class="btn btn-warning w-100" onclick="handleAction('AprobadoConObs')">Aprobado con Observaciones</button>
                        </div>
                        <div class="col-12 col-md-6 col-lg-3 mb-3">
                            <button class="btn btn-danger w-100" onclick="handleAction('Denegada')">Denegada</button>
                        </div>
                        <div class="col-12 col-md-6 col-lg-3 mb-3">
                            <button class="btn btn-secondary w-100" onclick="handleAction('Anulada')">Anulada</button>
                        </div>

                        <div class="modal fade" id="accionAprob" tabindex="-1" aria-labelledby="accionAprobLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="accionAprobLabel">Modal title</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="contenidoHandle"></div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                        <button type="button" class="btn btn-primary" onclick="guardarEstado()">Guardar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- Modal de descarga con conteo regresivo -->
<div class="modal fade" id="modalDescarga" tabindex="-1" aria-labelledby="modalDescargaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDescargaLabel">Descarga en progreso</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                La descarga comenzará en <span id="countdown">5</span> segundos.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('public/js/verSolicitud.js') ?>"></script>