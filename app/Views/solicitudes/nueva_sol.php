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
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Nueva Solicitud</h1>
    <p class="mb-4">Ingrese todos los datos solicitados.</p>
    <div class="card shadow mb-4">
        <div class="accordion" id="accordionSolicitud">
            <div class="card datos_personales">
                <div class="card-header" id="headingOne">
                    <h2 class="mb-0">
                        <button class="btn btn-block text-left" type="button" data-toggle="collapse" data-target="#datosPersonales" aria-expanded="true" aria-controls="datosPersonales">
                            I. DATOS PERSONALES
                        </button>
                    </h2>
                </div>

                <input type="text" class="form-control" id="id_cliente" name="id_cliente" disabled hidden>
                <div id="datosPersonales" class="collapse show" aria-labelledby="headingOne" data-parent="#datosPersonales">
                    <div class="card-body">
                        <div class="row  mb-4">
                            <div class="col-sm-6 d-flex justify-content-start">
                                <div class="form-inline my-2 my-lg-0">
                                    <input class="form-control mr-sm-2 duiG" type="text" id="duiBuscarCliente" placeholder="Ingrese el DUI buscar" aria-label="Search">
                                    <button class="btn btn-outline-primary my-2 my-sm-0" onclick="buscarCliente()">Buscar cliente</button>
                                </div>
                            </div>
                            <div class="col-sm-6 d-flex justify-content-end">
                                <div class="form-group">
                                    <label for="creacionDocumento">Fecha creación</label>
                                    <input type="date" class="form-control" id="creacionDocumento" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="nombrePersonal">Nombre</label>
                                <input type="text" class="form-control" id="nombrePersonal" disabled>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="duiPersonal">DUI</label>
                                <input type="text" class="form-control duiG" id="duiPersonal" disabled>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="fechaNacimiento">Fecha nacimientos</label>
                                <input type="date" class="form-control" id="fechaNacimiento" disabled>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="direccionActual">Dirección actual</label>
                                <input type="text" class="form-control" id="direccionActual" disabled>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="deptoCliente">Departamento</label>
                                <select id="deptoCliente" class="form-control" disabled>
                                    <option selected>Seleccione...</option>
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="muniCliente">Municipio</label>
                                <select id="muniCliente" class="form-control" disabled>
                                    <option selected>Seleccione...</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label for="telefono">Telefono</label>
                                <input type="text" class="form-control telG" id="telefono" disabled>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="Cpropia">Vive en casa propia</label>
                                <select id="Cpropia" class="form-control" disabled>
                                    <option values="-1" selected>Seleccione...</option>
                                    <option value="SI">SI</option>
                                    <option value="NO">NO</option>
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="CpromesaVenta">o en promesa de venta</label>
                                <select id="CpromesaVenta" class="form-control" disabled>
                                    <option values="-1" selected>Seleccione...</option>
                                    <option value="SI">SI</option>
                                    <option value="NO">NO</option>
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="Calquilada">Alquilada</label>
                                <select id="Calquilada" class="form-control" disabled>
                                    <option values="-1" selected>Seleccione...</option>
                                    <option value="SI">SI</option>
                                    <option value="NO">NO</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-5">
                                <label for="aQuienPertenece">A quien</label>
                                <input type="text" class="form-control" id="aQuienPertenece" disabled>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="telPropietario">Tel. del propietario</label>
                                <input type="text" class="form-control telG" id="telPropietario" disabled>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="tiempoDeVivirDomicilio">Tiempo de vivir en el domicilio</label>
                                <input type="text" class="form-control" id="tiempoDeVivirDomicilio" disabled>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="direccionAnterior">Dirección anterior (Si la actual es menor a dos años)</label>
                            <input type="text" class="form-control" id="direccionAnterior" disabled>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="estadoCivil">Estado Civil</label>
                                <select id="estadoCivil" class="form-control" disabled>
                                    <option selected>Seleccione...</option>
                                    <option value="Soltera/o">Soltera/o</option>
                                    <option value="Casada/o">Casada/o</option>
                                    <option value="Acompañada/o">Acompañada/o</option>
                                    <option value="Viuda/o">Viuda/o</option>
                                </select>
                            </div>
                            <div class="form-group col-md-8">
                                <label for="nombreConyugue">Nombre del cónyugue</label>
                                <input type="text" class="form-control" id="nombreConyugue" disabled>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-9">
                                <label for="dirTrabajoConyugue">Lugar y direccion de trabajo del conyugue</label>
                                <input type="text" class="form-control" id="dirTrabajoConyugue" disabled>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="telTrabajoConyugue">Tel. trabajo del conyugue</label>
                                <input type="text" class="form-control telG" id="telTrabajoConyugue" disabled>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="nombresPadres">Nombre del Padre o Madre</label>
                                <input type="text" class="form-control" id="nombresPadres" disabled>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-9">
                                <label for="direccionDeLosPadres">Direccion de los padres</label>
                                <input type="text" class="form-control" id="direccionDeLosPadres" disabled>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="telPadres">Tel. de los padres</label>
                                <input type="text" class="form-control telG" id="telPadres" disabled>
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
                                <input type="text" class="form-control" id="profesionOficio" name="profesion_oficio">
                            </div>
                            <div class="form-group col-md-8">
                                <label for="lugarTrabajo">Patron/Empresa/Lugar de trabajo</label>
                                <input type="text" class="form-control" id="lugarTrabajo" name="empresa">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="direccionDeTrabajo">Direccion del trabajo</label>
                                <input type="text" class="form-control" id="direccionDeTrabajo" name="direccion_trabajo">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label for="telTrabajo">Telefono del trabajo</label>
                                <input type="text" class="form-control telG" id="telTrabajo" name="telefono_trabajo">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="cargoDesempeña">Cargo que desempeña</label>
                                <input type="text" class="form-control" id="cargoDesempeña" name="cargo">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="salario">Salario</label>
                                <input type="text" class="form-control montosG" id="salario" name="salario">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="tiempoDeLaborar">Tiempo de laborar en la empresa</label>
                                <input type="text" class="form-control" id="tiempoDeLaborar" name="tiempo_laborando_empresa">
                            </div>
                            <div class="form-group col-md-8">
                                <label for="nombreJefe">Nombre del jefe inmediato</label>
                                <input type="text" class="form-control" id="nombreJefe" name="nombre_jefe_inmediato">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-9">
                                <label for="empresaAnterior">Empresa en que laboro anteriormente</label>
                                <input type="text" class="form-control" id="empresaAnterior" name="empresa_anterior">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="telEmpresaAnterior">Tel. de la empresa anterior</label>
                                <input type="text" class="form-control telG" id="telEmpresaAnterior" name="telefono_empresa_anterior">
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
                                <input type="text" class="form-control" id="nombreRefFamiliarUno" name="nombre">
                            </div>
                            <div class="form-group col-md-2">
                                <label for="parentescoRefFamiliarUno">Prentesco</label>
                                <select id="parentescoRefFamiliarUno" class="form-control" name="parentesco">
                                    <option value="PAPÁ">PAPÁ</option>
                                    <option value="MAMÁ">MAMÁ</option>
                                    <option value="HERMANO/A">HERMANO/A</option>
                                    <option value="TIO/A">TIO/A</option>
                                    <option value="PRIMO/A">PRIMO/A</option>
                                    <option value="ABUELO/A">ABUELO/A</option>
                                </select>
                            </div>
                            <div class="form-group col-md-5">
                                <label for="dirRefFamiliarUno">Direccion</label>
                                <input type="text" class="form-control" id="dirRefFamiliarUno" name="direccion">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label for="telProRefFamiliarUno">Telefono</label>
                                <input type="text" class="form-control telG" id="telProRefFamiliarUno" name="telefono">
                            </div>
                            <div class="form-group col-md-5">
                                <label for="lugarTrabajoRefFamiliarUno">Lugar de trabajo</label>
                                <input type="text" class="form-control" id="lugarTrabajoRefFamiliarUno" name="lugar_trabajo">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="telTrabaRefFamiliarUno">Telefono trabajo</label>
                                <input type="text" class="form-control telG" id="telTrabaRefFamiliarUno" name="telefono_trabajo">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-5">
                                <label for="nombreRefFamiliarDos">2. Nombre</label>
                                <input type="text" class="form-control" id="nombreRefFamiliarDos" name="nombre">
                            </div>
                            <div class="form-group col-md-2">
                                <label for="parentescoRefFamiliarDos">Prentesco</label>
                                <select id="parentescoRefFamiliarDos" class="form-control" name="parentesco">
                                    <option value="PAPÁ">PAPÁ</option>
                                    <option value="MAMÁ">MAMÁ</option>
                                    <option value="HERMANO/A">HERMANO/A</option>
                                    <option value="TIO/A">TIO/A</option>
                                    <option value="PRIMO/A">PRIMO/A</option>
                                    <option value="ABUELO/A">ABUELO/A</option>
                                </select>
                            </div>
                            <div class="form-group col-md-5">
                                <label for="dirRefFamiliarDos">Direccion</label>
                                <input type="text" class="form-control" id="dirRefFamiliarDos" name="direccion">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label for="telProRefFamiliarDos">Telefono</label>
                                <input type="text" class="form-control telG" id="telProRefFamiliarDos" name="telefono">
                            </div>
                            <div class="form-group col-md-5">
                                <label for="lugarTrabajoRefFamiliarDos">Lugar de trabajo</label>
                                <input type="text" class="form-control" id="lugarTrabajoRefFamiliarDos" name="lugar_trabajo">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="telTrabaRefFamiliarDos">Telefono trabajo</label>
                                <input type="text" class="form-control telG" id="telTrabaRefFamiliarDos" name="telefono_trabajo">
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
                        <div class="form-row">
                            <div class="form-group col-md-5">
                                <label for="nombreRefNoFamiliarUno">1. Nombre</label>
                                <input type="text" class="form-control" id="nombreRefNoFamiliarUno" name="nombre">
                            </div>
                            <div class="form-group col-md-7">
                                <label for="dirRefNoFamiliarUno">Direccion</label>
                                <input type="text" class="form-control" id="dirRefNoFamiliarUno" name="direccion">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-2">
                                <label for="telProRefNoFamiliarUno">Telefono</label>
                                <input type="text" class="form-control telG" id="telProRefNoFamiliarUno" name="telefono">
                            </div>
                            <div class="form-group col-md-8">
                                <label for="lugarTrabajoRefNoFamiliarUno">Lugar de trabajo</label>
                                <input type="text" class="form-control" id="lugarTrabajoRefNoFamiliarUno" name="lugar_trabajo">
                            </div>
                            <div class="form-group col-md-2">
                                <label for="telTrabaRefNoFamiliarUno">Telefono trabajo</label>
                                <input type="text" class="form-control telG" id="telTrabaRefNoFamiliarUno" name="telefono_trabajo">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-5">
                                <label for="nombreRefNoFamiliarDos">2. Nombre</label>
                                <input type="text" class="form-control" id="nombreRefNoFamiliarDos" name="nombre">
                            </div>
                            <div class="form-group col-md-7">
                                <label for="dirRefNoFamiliarDos">Direccion</label>
                                <input type="text" class="form-control" id="dirRefNoFamiliarDos" name="direccion">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-2">
                                <label for="telProRefNoFamiliarDos">Telefono</label>
                                <input type="text" class="form-control telG" id="telProRefNoFamiliarDos" name="telefono">
                            </div>
                            <div class="form-group col-md-8">
                                <label for="lugarTrabajoRefNoFamiliarDos">Lugar de trabajo</label>
                                <input type="text" class="form-control" id="lugarTrabajoRefNoFamiliarDos" name="lugar_trabajo">
                            </div>
                            <div class="form-group col-md-2">
                                <label for="telTrabaRefNoFamiliarDos">Telefono trabajo</label>
                                <input type="text" class="form-control telG" id="telTrabaRefNoFamiliarDos" name="telefono_trabajo">
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
                                            <td><input type="text" class="form-control" id="refCrediticiaNombre1" name="institucion"></td>
                                            <td><input type="text" class="form-control telG" id="refCrediticiaTel1" name="telefono"></td>
                                            <td><input type="text" class="form-control montosG" id="refCrediticiaMonto1" name="monto_credito"></td>
                                            <td><input type="text" class="form-control" id="refCrediticiaPeriodo1" name="periodos"></td>
                                            <td><input type="text" class="form-control" id="refCrediticiaPlazo1" name="plazo"></td>
                                            <td>
                                                <div class="form-group col-md">
                                                    <select id="refCrediticiaEstado1" class="form-control" name="estado">
                                                        <option value="ACTIVA">ACTIVA</option>
                                                        <option value="CANCELADA">CANCELADA</option>
                                                    </select>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><input type="text" class="form-control" id="refCrediticiaNombre2" name="institucion"></td>
                                            <td><input type="text" class="form-control telG" id="refCrediticiaTel2" name="telefono"></td>
                                            <td><input type="text" class="form-control montosG" id="refCrediticiaMonto2" name="monto_credito"></td>
                                            <td><input type="text" class="form-control" id="refCrediticiaPeriodo2" name="periodos"></td>
                                            <td><input type="text" class="form-control" id="refCrediticiaPlazo2" name="plazo"></td>
                                            <td>
                                                <div class="form-group col-md">
                                                    <select id="refCrediticiaEstado2" class="form-control" name="estado">
                                                        <option value="ACTIVA">ACTIVA</option>
                                                        <option value="CANCELADA">CANCELADA</option>
                                                    </select>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><input type="text" class="form-control" id="refCrediticiaNombre3" name="institucion"></td>
                                            <td><input type="text" class="form-control telG" id="refCrediticiaTel3" name="telefono"></td>
                                            <td><input type="text" class="form-control montosG" id="refCrediticiaMonto3" name="monto_credito"></td>
                                            <td><input type="text" class="form-control" id="refCrediticiaPeriodo3" name="periodos"></td>
                                            <td><input type="text" class="form-control" id="refCrediticiaPlazo3" name="plazo"></td>
                                            <td>
                                                <div class="form-group col-md">
                                                    <select id="refCrediticiaEstado3" class="form-control" name="estado">
                                                        <option value="ACTIVA">ACTIVA</option>
                                                        <option value="CANCELADA">CANCELADA</option>
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
                                                    <td><input type="text" class="form-control montosG ingresos" id="ingresoMensual" name="ingresoMensual"></td>
                                                    <td>Egreso Mensual</td>
                                                    <td><input type="text" class="form-control montosG egresos" id="egresoMensual" name="egresoMensual"></td>
                                                </tr>
                                                <tr>
                                                    <td>Salario</td>
                                                    <td><input type="text" class="form-control montosG ingresos" id="salarioIng" name="salarioIng"></td>
                                                    <td>Pago de Casa</td>
                                                    <td><input type="text" class="form-control montosG egresos" id="pagoCasa" name="pagoCasa"></td>
                                                </tr>
                                                <tr>
                                                    <td>Otros (Explique)</td>
                                                    <td><input type="text" class="form-control" id="otrosIngresos" name="otrosIngresos"></td>
                                                    <td>Gastos de Vida</td>
                                                    <td><input type="text" class="form-control montosG egresos" id="gastosVida" name="gastosVida"></td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td>Otros:</td>
                                                    <td><input type="text" class="form-control montosG egresos" id="otrosEgresos" name="otrosEgresos"></td>
                                                </tr>
                                                <tr>
                                                    <td>Total de Ingresos</td>
                                                    <td><input type="text" disabled class="form-control montosG" id="totalIngresos" name="totalIngresos"></td>
                                                    <td>Total de Egresos</td>
                                                    <td><input type="text" disabled class="form-control montosG" id="totalEgresos" name="totalEgresos"></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3">Diferencia Ingresos - Egresos:</td>
                                                    <td><input disabled type="text" class="form-control montosG" id="diferencia" name="diferencia"></td>
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
                                    <div class="col-sm-4 text-right">
                                        <button type="button" class="btn btn-primary" id="btnAgregarProductoMdl" data-toggle="modal" data-target="#agregarProductoTemp">
                                            Agregar producto
                                        </button>
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
                                                <th scope="col">Opcion</th>
                                            </tr>
                                        </thead>
                                        <tbody>

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
                                                <label for="valorArticulo">Valor del articulo:</label>
                                                <input type="text" class="form-control" id="valorArticulo" disabled>
                                            </div>
                                            <div class="form-group col-md-12">
                                                <label for="pagoPrima">Valor/pago de prima</label>
                                                <input type="text" class="form-control montosG" id="valorPagoPrima">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="saldoAPagar">Saldo a pagar</label>
                                                <input type="text" class="form-control" id="saldoAPagar" disabled>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-12 col-sm-6 col-md-4">
                                                <label for="cantidadCuotas">Cantidad cuotas</label>
                                                <select class="form-control" id="cantidadCuotas" name="cantidadCuotas">
                                                </select>
                                            </div>
                                            <div class="form-group col-12 col-sm-6 col-md-4">
                                                <label for="montoCuota">Monto cuotas de:</label>
                                                <input type="text" class="form-control" id="montoCuota" disabled>
                                            </div>
                                            <div class="form-group col-12 col-sm-6 col-md-4">
                                                <label for="montoTotalPagar">Monto total a pagar:</label>
                                                <input type="text" class="form-control" id="montoTotalPagar" disabled>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-12 col-sm">
                                                <label for="observaciones">Observación</label>
                                                <textarea class="form-control" id="observaciones" rows="4" placeholder="Ingrese su observación aquí..."></textarea>
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
                                <input type="text" class="form-control" id="nombre" name="nombre">
                            </div>
                            <div class="form-group col-md-2">
                                <label for="dui">DUI:</label>
                                <input type="text" class="form-control duiG" id="dui" name="dui">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="direccion">Dirección:</label>
                            <input type="text" class="form-control" id="direccion" name="direccion">
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-2">
                                <label for="COtelPersonal">Tel. personal</label>
                                <input type="text" class="form-control telG" id="COtelPersonal">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="COpropiaCN">Vive en casa propia</label>
                                <select id="COpropiaCN" class="form-control">
                                    <option value="-1" selected>Seleccione...</option>
                                    <option value="SI">SI</option>
                                    <option value="NO">NO</option>
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="COpromesaVenta">o en promesa de venta</label>
                                <select id="COpromesaVenta" class="form-control">
                                    <option value="-1" selected>Seleccione...</option>
                                    <option value="SI">SI</option>
                                    <option value="NO">NO</option>
                                </select>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="COalquilada">Alquilada</label>
                                <select id="COalquilada" class="form-control">
                                    <option value="-1" selected>Seleccione...</option>
                                    <option value="SI">SI</option>
                                    <option value="NO">NO</option>
                                </select>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="COtiempoVivienda">Tiempo</label>
                                <input type="text" class="form-control" id="COtiempoVivienda">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label for="COestadoCivil">Estado Civil</label>
                                <select id="COestadoCivil" class="form-control">
                                    <option value="-1" selected>Seleccione...</option>
                                    <option value="Soltera/o">Soltera/o</option>
                                    <option value="Casada/o">Casada/o</option>
                                    <option value="Acompañada/o">Acompañada/o</option>
                                    <option value="Viuda/o">Viuda/o</option>
                                </select>
                            </div>
                            <div class="form-group col-md-9">
                                <label for="nombreConyugueCN">Nombre del cónyugue</label>
                                <input type="text" class="form-control" id="nombreConyugueCN">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="profesion">Profesión u oficio:</label>
                                <input type="text" class="form-control" id="profesion" name="profesion">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="patrono">Patrono/Empresa/Lugar de Trabajo:</label>
                                <input type="text" class="form-control" id="patrono" name="patrono">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-9">
                                <label for="COdireccionTrabajo">Dirección del trabajo:</label>
                                <input type="text" class="form-control" id="COdireccionTrabajo" name="COdireccionTrabajo">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="COtelefonoTrabajo">Telefono Trabajo:</label>
                                <input type="text" class="form-control telG" id="COtelefonoTrabajo" name="COtelefonoTrabajo">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="COcargoDesempeña">Cargo que desempeña:</label>
                                <input type="text" class="form-control" id="COcargoDesempeña" name="COcargoDesempeña">
                            </div>
                            <div class="form-group col-md-2">
                                <label for="COsalario">Salario:</label>
                                <input type="text" class="form-control montosG" id="COsalario" name="COsalario">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="COnombreJefe">Nombre Jefe inmediato:</label>
                                <input type="text" class="form-control" id="COnombreJefe" name="COnombreJefe">
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
                                        <input type="text" class="form-control coref1" id="COnombreRef1" name="COnombreRef1">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="COparentescoRef1">Parentesco</label>
                                        <select id="COparentescoRef1" class="form-control coref1">
                                            <option value="PAPÁ">PAPÁ</option>
                                            <option value="MAMÁ">MAMÁ</option>
                                            <option value="HERMANO/A">HERMANO/A</option>
                                            <option value="TIO/A">TIO/A</option>
                                            <option value="PRIMO/A">PRIMO/A</option>
                                            <option value="ABUELO/A">ABUELO/A</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-10">
                                        <label for="COdireccionRef1">Direccion:</label>
                                        <input type="text" class="form-control coref1" id="COdireccionRef1" name="COdireccionRef1">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="COtelRef1">Telefono:</label>
                                        <input type="text" class="form-control coref1 telG" id="COtelRef1" name="COtelRef1">
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="form-row">
                                    <div class="form-group col-md-10">
                                        <label for="COnombreRef2">b) Nombre:</label>
                                        <input type="text" class="form-control coref2" id="COnombreRef2" name="COnombreRef2">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="COparentescoRef2">Parentesco</label>
                                        <select id="COparentescoRef2" class="form-control coref2">
                                            <option value="PAPÁ">PAPÁ</option>
                                            <option value="MAMÁ">MAMÁ</option>
                                            <option value="HERMANO/A">HERMANO/A</option>
                                            <option value="TIO/A">TIO/A</option>
                                            <option value="PRIMO/A">PRIMO/A</option>
                                            <option value="ABUELO/A">ABUELO/A</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-10">
                                        <label for="COdireccionRef2">Direccion:</label>
                                        <input type="text" class="form-control coref2" id="COdireccionRef2" name="COdireccionRef2">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="COtelRef2">Telefono:</label>
                                        <input type="text" class="form-control coref2 telG" id="COtelRef2" name="COtelRef2">
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="form-row">
                                    <div class="form-group col-md-10">
                                        <label for="COnombreRef3">c) Nombre:</label>
                                        <input type="text" class="form-control coref3" id="COnombreRef3" name="COnombreRef3">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="COparentescoRef3">Parentesco</label>
                                        <select id="COparentescoRef3" class="form-control coref3">
                                            <option value="PAPÁ">PAPÁ</option>
                                            <option value="MAMÁ">MAMÁ</option>
                                            <option value="HERMANO/A">HERMANO/A</option>
                                            <option value="TIO/A">TIO/A</option>
                                            <option value="PRIMO/A">PRIMO/A</option>
                                            <option value="ABUELO/A">ABUELO/A</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-10">
                                        <label for="COdireccionRef3">Direccion:</label>
                                        <input type="text" class="form-control coref3" id="COdireccionRef3" name="COdireccionRef3">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="COtelRef3">Telefono:</label>
                                        <input type="text" class="form-control telG coref3" id="COtelRef3" name="COtelRef3">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <div class="row">
            <div class="col-sm">
                <button class="btn btn-primary float-right btn-block" id="guardar_solicitud" type="button">Guardar Solicitud</button>
            </div>
        </div>
    </div>
</div>
<!-- Vertically centered scrollable modal -->
<div class="modal fade" id="agregarProductoTemp" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="agregarProductoTempLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="agregarProductoTempLabel">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-inline">
                    <div class="form-group mx-sm-3 mb-2">
                        <input type="text" class="form-control" id="buscar_producto" placeholder="Buscar producto">
                    </div>
                    <button type="button" class="btn btn-primary mb-2" onclick="buscarProducto()">Buscar</button>
                </div>
                <div class="row">
                    <div class="col sm">
                        <b>Productos agregados: <p id="prodAgregadosCant"></p></b>
                    </div>
                </div>

                <div class="form-inline">
                    <table class="table" id="dataTableBusquedaProducto">
                        <thead>
                            <tr>
                                <th scope="col">Codigo</th>
                                <th scope="col">Nombre</th>
                                <th scope="col">Marca</th>
                                <th scope="col">Modelo</th>
                                <th scope="col">Color</th>
                                <th scope="col">Precio</th>
                                <th scope="col">Disponible</th>
                                <th scope="col">Cantidad solicitada</th>
                                <th scope="col">Opcion</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="agregarProdASolicitud" onclick="confirmarAgregarProducto()">Agregar productos a la solicitud</button>
            </div>
        </div>
    </div>
</div>
<script src="<?= base_url('public/js/nueva_sol.js') ?>"></script>