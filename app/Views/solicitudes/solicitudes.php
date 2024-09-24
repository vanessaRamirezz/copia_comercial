<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Solicitudes</h1>
    <p class="mb-4">Se visualizaran todas tus solicitudes.</p>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <a href="<?php echo base_url('nueva_solicitud'); ?>" class="btn btn-primary" id="btnNuevaSolicitud">Crear nueva solicitud</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm" id="dataTableSol" width="100%" cellspacing="0">
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
                        <?php if (!empty($solicitudes)) : ?>
                            <?php
                            $contador = 1;
                            foreach ($solicitudes as $solicitud) : ?>
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
                                        <i class="fas fa-eye icono-solicitud" style="cursor: pointer;" title="Ver solicitud" onclick="redirectToSolicitud('<?= $solicitud['id_solicitud']; ?>')"></i>
                                        <i class="fa-solid fa-download icono-solicitud" style="cursor: pointer;" title="Descargar solicitud de credito" onclick="redirectToSolicitudDocSol('<?= $solicitud['id_solicitud']; ?>')"></i>
                                        <?php if (!empty($solicitud['observacion'])) : ?>
                                            <i class="fa-regular fa-message icono-solicitud" data-toggle="modal" data-target="#verObservaciones<?= $contador; ?>" style="cursor: pointer;" title="Ver observacion" onclick="verObservacion('<?= $solicitud['observacion']; ?>')"></i>
                                        <?php endif; ?>
                                        <?php if (!empty($solicitud['num_contrato'])) : ?>
                                            <i class="fa-solid fa-file-arrow-down icono-solicitud" style="cursor: pointer;" title="Descargar contrato" onclick="descargarContrato('<?= $solicitud['numero_solicitud']; ?>')"></i>
                                        <?php endif; ?>
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
                                                            <textarea class="form-control" id="exampleFormControlTextarea1" disabled rows="3"><?= $solicitud['observacion']?></textarea>
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