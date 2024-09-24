<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Clientes por sucursal</h1>
    <p class="mb-4">Se visualizaran todos los clientes de la sucursal.</p>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <a href="<?php echo base_url('nuevo_cliente'); ?>" class="btn btn-primary" id="btnNuevaSolicitud">Crear nuevo cliente</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>DUI</th>
                            <th>Nombre completo</th>
                            <th>Telefono</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dataClientes as $cliente) : ?>
                            <tr>
                                <td><?= $cliente['dui'] ?></td>
                                <td><?= $cliente['nombre_completo'] ?></td>
                                <td><?= $cliente['telefono'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
