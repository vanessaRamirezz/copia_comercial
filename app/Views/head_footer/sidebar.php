<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Creditos <sup>V1</sup></div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">
    <?php
    usort($dataMenu, function ($a, $b) {
        return $a['orden_acceso'] <=> $b['orden_acceso'];
    });

    foreach ($dataMenu as $menuItem) {
        echo '<li class="nav-item">';
        echo '<a class="nav-link" href="' . base_url($menuItem['url_acceso']) . '">';
        echo '<i class="' . $menuItem['icono'] . '"></i>';
        echo '<span>' . $menuItem['acceso'] . '</span></a>';
        echo '</li>';
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