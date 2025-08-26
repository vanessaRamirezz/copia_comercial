<style>
    /* Estilo para las tablas */
    .table th, .table td {
        font-size: 12px; /* Tamaño de fuente más pequeño */
    }
</style>
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
                                    <tbody id="tbodySolicitudes">
                                        
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
                                <table class="table table-bordered table-sm" id="dataTableSolVariasTab" width="100%" cellspacing="0">
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
                                    <tbody id="dataTableSolVarias">
                                        
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

<!-- Modal Reutilizable -->
<div class="modal fade" id="modalPagare" tabindex="-1" role="dialog" aria-labelledby="modalPagareLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalPagareLabel">Pagaré</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="modalPagareBody">
        <!-- Aquí insertaremos el contenido del pagaré -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.js"></script>
<script src="<?= base_url('public/js/solicitudes.js') ?>"></script>