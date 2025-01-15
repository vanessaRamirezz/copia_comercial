<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Solicitudes</h1>
    <p class="mb-4">Se visualizaran todas tus solicitudes.</p>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <a href="<?php echo base_url('nueva_solicitud'); ?>" class="btn btn-primary" id="btnNuevaSolicitud">Crear nueva solicitud</a>
            <button type="button" class="btn btn-warning" id="refrescarSolicitud" onclick="recargarSoli()"><i class="fa-solid fa-rotate-right"></i></button>
        </div>

        <div class="card-body">
            <div class="accordion" id="accordionExample">
                <div class="card">
                    <div class="card-header" id="headingOne">
                        <h2 class="mb-0">
                            <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                Solicitudes creadas
                            </button>
                        </h2>
                    </div>

                    <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm" id="dataTableSolCreadas" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th class="text-nowrap">No Solicitud</th>
                                            <th class="text-nowrap">DUI</th>
                                            <th class="text-nowrap">Cliente</th>
                                            <th class="text-nowrap">Fecha creación</th>
                                            <th class="text-nowrap">Estado</th>
                                            <th class="text-nowrap">Usuario creador</th>
                                            <th class="text-nowrap">Operaciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($solicitudesCreadas)) : ?>
                                            <?php
                                            $contador = 1;
                                            foreach ($solicitudesCreadas as $solicitud) : ?>
                                                <tr>
                                                    <td><?= esc($solicitud['numero_solicitud']); ?></td>
                                                    <td><?= esc($solicitud['dui']); ?></td>
                                                    <td><?= esc($solicitud['nombre_completo']); ?></td>
                                                    <td><?= esc($solicitud['fecha_creacion']); ?></td>
                                                    <td><?php
                                                        if ($solicitud['id_estado_actual'] == 1) {
                                                            echo '<span style="color: blue;">' . esc($solicitud['estado']) . ' <i class="fa-solid fa-check"></i></span>';
                                                        } else if ($solicitud['id_estado_actual'] == 2) {
                                                            echo '<span style="color: green;">' . esc($solicitud['estado']) . ' <i class="fa-solid fa-check-double"></i></span>';
                                                        } else if ($solicitud['id_estado_actual'] == 3) {
                                                            echo '<span style="color: red;">' . esc($solicitud['estado']) . ' <i class="fa-solid fa-ban"></i></span>';
                                                        } else if ($solicitud['id_estado_actual'] == 4) {
                                                            echo '<span style="color: red;">' . esc($solicitud['estado']) . ' <i class="fa-solid fa-ban"></i></span>';
                                                        } else if ($solicitud['id_estado_actual'] == 5) {
                                                            echo '<span style="color: #FFA500;">' . esc($solicitud['estado']) . ' <i class="fa-solid fa-check-double"></i></span>';
                                                        }

                                                        ?></td>
                                                    <td><?= esc($solicitud['user_creador']); ?></td>
                                                    <td>
                                                        <div class="dropdown">
                                                            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton<?= $solicitud['id_solicitud']; ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                Acciones
                                                            </button>
                                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton<?= $solicitud['id_solicitud']; ?>">
                                                                <!-- Ver solicitud -->
                                                                <a class="dropdown-item" href="#" onclick="redirectToSolicitud('<?= $solicitud['id_solicitud']; ?>')">
                                                                    <i class="fas fa-eye"></i> Ver solicitud
                                                                </a>

                                                                <!-- Descargar solicitud de crédito -->
                                                                <a class="dropdown-item" href="#" onclick="redirectToSolicitudDocSol('<?= $solicitud['id_solicitud']; ?>')">
                                                                    <i class="fa-solid fa-download"></i> Descargar solicitud de crédito
                                                                </a>

                                                                <!-- Ver observación (solo si existe) -->
                                                                <?php if (!empty($solicitud['observacion'])) : ?>
                                                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#verObservaciones<?= $contador; ?>" onclick="verObservacion('<?= $solicitud['observacion']; ?>')">
                                                                        <i class="fa-regular fa-message"></i> Ver observación
                                                                    </a>
                                                                <?php endif; ?>

                                                                <!-- Descargar contrato (solo si existe) -->
                                                                <?php if (!empty($solicitud['num_contrato'])) : ?>
                                                                    <a class="dropdown-item" href="#" onclick="descargarContrato('<?= $solicitud['numero_solicitud']; ?>')">
                                                                        <i class="fa-solid fa-file-arrow-down"></i> Descargar contrato
                                                                    </a>
                                                                <?php else : ?>
                                                                    <a class="dropdown-item" href="#" onclick="generarContrato('<?= $solicitud['id_solicitud']; ?>')">
                                                                        <i class="fa-solid fa-file-circle-plus"></i> Generar contrato
                                                                    </a>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </td>

                                                </tr>
                                                <div class="modal fade" id="verObservaciones<?= $contador; ?>" tabindex="-1" aria-labelledby="verObservacionesLabel<?= $contador; ?>" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="verObservacionesLabel<?= $contador; ?>">Observacion solicitud <?= esc($solicitud['numero_solicitud']); ?></h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col-sm">
                                                                        <div class="form-group">
                                                                            <label for="exampleFormControlTextarea1">Observaciones realizadas</label>
                                                                            <textarea class="form-control" id="exampleFormControlTextarea1" disabled rows="3"><?= $solicitud['observacion'] ?></textarea>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php $contador++;
                                            endforeach; ?>
                                        <?php else : ?>
                                            <tr>
                                                <td colspan="7">No hay solicitudes disponibles.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header" id="headingTwo">
                        <h2 class="mb-0">
                            <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                Solicitudes varias
                            </button>
                        </h2>
                    </div>
                    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm" id="dataTableSolVarias" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th class="text-nowrap">No Solicitud</th>
                                            <th class="text-nowrap">DUI</th>
                                            <th class="text-nowrap">Cliente</th>
                                            <th class="text-nowrap">Fecha creación</th>
                                            <th class="text-nowrap">Estado</th>
                                            <th class="text-nowrap">Usuario creador</th>
                                            <th class="text-nowrap">Operaciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($solicitudesVarias)) : ?>
                                            <?php
                                            $contador = 1;
                                            foreach ($solicitudesVarias as $solicitudVar) : ?>
                                                <tr>
                                                    <td><?= esc($solicitudVar['numero_solicitud']); ?></td>
                                                    <td><?= esc($solicitudVar['dui']); ?></td>
                                                    <td><?= esc($solicitudVar['nombre_completo']); ?></td>
                                                    <td><?= esc($solicitudVar['fecha_creacion']); ?></td>
                                                    <td><?php
                                                        if ($solicitudVar['id_estado_actual'] == 1) {
                                                            echo '<span style="color: blue;">' . esc($solicitudVar['estado']) . ' <i class="fa-solid fa-check"></i></span>';
                                                        } else if ($solicitudVar['id_estado_actual'] == 2) {
                                                            echo '<span style="color: green;">' . esc($solicitudVar['estado']) . ' <i class="fa-solid fa-check-double"></i></span>';
                                                        } else if ($solicitudVar['id_estado_actual'] == 3) {
                                                            echo '<span style="color: red;">' . esc($solicitudVar['estado']) . ' <i class="fa-solid fa-ban"></i></span>';
                                                        } else if ($solicitudVar['id_estado_actual'] == 4) {
                                                            echo '<span style="color: red;">' . esc($solicitudVar['estado']) . ' <i class="fa-solid fa-ban"></i></span>';
                                                        } else if ($solicitudVar['id_estado_actual'] == 5) {
                                                            echo '<span style="color: #FFA500;">' . esc($solicitudVar['estado']) . ' <i class="fa-solid fa-check-double"></i></span>';
                                                        }

                                                        ?></td>
                                                    <td><?= esc($solicitudVar['user_creador']); ?></td>
                                                    <td>
                                                        <div class="dropdown">
                                                            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton<?= $solicitudVar['id_solicitud']; ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                Acciones
                                                            </button>
                                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton<?= $solicitudVar['id_solicitud']; ?>">
                                                                <!-- Ver solicitud -->
                                                                <a class="dropdown-item" href="#" onclick="redirectToSolicitud('<?= $solicitudVar['id_solicitud']; ?>')">
                                                                    <i class="fas fa-eye"></i> Ver solicitud
                                                                </a>

                                                                <!-- Descargar solicitud de crédito -->
                                                                <a class="dropdown-item" href="#" onclick="redirectToSolicitudDocSol('<?= $solicitudVar['id_solicitud']; ?>')">
                                                                    <i class="fa-solid fa-download"></i> Descargar solicitud de crédito
                                                                </a>

                                                                <!-- Ver observación (solo si existe) -->
                                                                <?php if (!empty($solicitudVar['observacion'])) : ?>
                                                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#verObservaciones<?= $contador; ?>" onclick="verObservacion('<?= $solicitudVar['observacion']; ?>')">
                                                                        <i class="fa-regular fa-message"></i> Ver observación
                                                                    </a>
                                                                <?php endif; ?>

                                                                <!-- Descargar contrato (solo si existe) -->
                                                                <?php if (!empty($solicitudVar['num_contrato'])) : ?>
                                                                    <a class="dropdown-item" href="#" onclick="descargarContrato('<?= $solicitudVar['numero_solicitud']; ?>')">
                                                                        <i class="fa-solid fa-file-arrow-down"></i> Descargar contrato
                                                                    </a>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>

                                                    </td>
                                                </tr>
                                                <div class="modal fade" id="verObservaciones<?= $contador; ?>" tabindex="-1" aria-labelledby="verObservacionesLabel<?= $contador; ?>" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="verObservacionesLabel<?= $contador; ?>">Observacion solicitud <?= esc($solicitudVar['numero_solicitud']); ?></h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col-sm">
                                                                        <div class="form-group">
                                                                            <label for="exampleFormControlTextarea1">Observaciones realizadas</label>
                                                                            <textarea class="form-control" id="exampleFormControlTextarea1" disabled rows="3"><?= $solicitudVar['observacion'] ?></textarea>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php $contador++;
                                            endforeach; ?>
                                        <?php else : ?>
                                            <tr>
                                                <td colspan="7">No hay solicitudes disponibles.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal de descarga con conteo regresivo -->
<div class="modal fade" id="modalDescarga" tabindex="-1" aria-labelledby="modalDescargaLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDescargaLabel">Preparando Descarga</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
            </div>
            <div class="modal-body">
                La descarga comenzará en <span id="countdown">5</span> segundos.
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('public/js/solicitudes.js') ?>"></script>