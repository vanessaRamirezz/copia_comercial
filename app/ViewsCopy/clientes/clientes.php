<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Clientes por sucursal</h1>
    <p class="mb-4">Se visualizaran todos los clientes de la sucursal.</p>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <a href="<?php echo base_url('nuevo_cliente'); ?>" class="btn btn-primary" id="btnNuevaSolicitud">Crear nuevo cliente</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="tableClientes" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>DUI</th>
                            <th>Nombre completo</th>
                            <th>Telefono</th>
                            <th>Opcion</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dataClientes as $cliente) : ?>
                            <tr>
                                <td><?= $cliente['dui'] ?></td>
                                <td><?= $cliente['nombre_completo'] ?></td>
                                <td><?= $cliente['telefono'] ?></td>
                                <td>
                                    <button type="button" class="btn btn-warning" onclick="window.location.href='<?= base_url('editar_cliente/' . base64_encode($cliente['id_cliente'])) ?>'">
                                        <i class="fa-regular fa-pen-to-square"></i> Editar
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        let table = $('#tableClientes').DataTable({
            "language": {
                "url": baseURL + "public/js/es-ES.json"
            },
            "stateSave": false, // Asegura que no guarda el estado previo
            "initComplete": function () {
                let searchInput = $('.dataTables_filter input');
                searchInput.val('').trigger('input'); // Limpia el buscador y actualiza la tabla
            }
        });

        // Forzar que el input de búsqueda se limpie cada vez que se recarga la tabla
        setTimeout(function () {
            let searchInput = $('.dataTables_filter input');
            searchInput.val('').trigger('input');
        }, 500);
    }); // ← Cierre correcto de la función
</script>
