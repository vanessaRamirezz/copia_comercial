<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= base_url(); ?>">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Creditos <sup>V1</sup></div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">
    <?php
    // Ordenar el menú por 'orden_acceso'
    usort($dataMenu, function ($a, $b) {
        return $a['orden_acceso'] <=> $b['orden_acceso'];
    });

    $agrupaciones = []; // Almacenar las agrupaciones con sus elementos
    $noAgrupados = []; // Almacenar los elementos sin agrupación

    foreach ($dataMenu as $menuItem) {
        if ($menuItem['agrupacion'] === 'NO_AGRUPAR') {
            $noAgrupados[] = $menuItem; // Guardar los elementos sin agrupación
        } else {
            $agrupaciones[$menuItem['agrupacion']][] = $menuItem; // Agrupar por categoría
        }
    }

    // Definir iconos para cada agrupación
    $iconosAgrupacion = [
        'MANTENIMIENTOS' => 'fa-solid fa-cogs',
        'MOVIMIENTOS' => 'fa-solid fa-dolly',
    ];

    // Mostrar los elementos sin agrupación
    foreach ($noAgrupados as $menuItem) {
        echo '<li class="nav-item">';
        echo '<a class="nav-link" href="' . base_url($menuItem['url_acceso']) . '">';
        echo '<i class="' . $menuItem['icono'] . '"></i>';
        echo '<span>' . $menuItem['acceso'] . '</span></a>';
        echo '</li>';
    }

    // Mostrar los elementos agrupados
    foreach ($agrupaciones as $grupo => $items) {
        $collapseId = "collapse_" . preg_replace('/\s+/', '_', strtolower($grupo)); // Generar ID único
        $iconoGrupo = $iconosAgrupacion[$grupo] ?? 'fa-solid fa-folder'; // Ícono por defecto si no está definido

        echo '<li class="nav-item">';
        echo '<a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#' . $collapseId . '"';
        echo ' aria-expanded="true" aria-controls="' . $collapseId . '">';
        echo '<i class="' . $iconoGrupo . '"></i>'; // Ícono de la agrupación
        echo '<span>' . ucfirst(strtolower($grupo)) . '</span>';
        echo '</a>';

        echo '<div id="' . $collapseId . '" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">';
        echo '<div class="bg-white py-2 collapse-inner rounded">';
        echo '<h6 class="collapse-header">' . $grupo . ':</h6>'; // Mostrar nombre del grupo

        foreach ($items as $menuItem) {
            echo '<a class="collapse-item" href="' . base_url($menuItem['url_acceso']) . '">';
            echo '<i class="' . $menuItem['icono'] . '"></i> ' . $menuItem['acceso'] . '</a>';
        }

        echo '</div></div></li>';
    }
    ?>


    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

    <!-- Main Content -->
    <div id="content">