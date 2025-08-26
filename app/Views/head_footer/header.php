<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Jaime Palacios">
    <link rel="icon" href="<?= base_url('public/img/logo.ico'); ?>" type="image/x-icon">


    <title>Todo para el Hogar</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this page -->
    <link rel="stylesheet" href="<?= base_url('public/css/sb-admin-2.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('public/vendor/datatables/dataTables.bootstrap4.min.css') ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet">
    <script>
        var baseURL = '<?= base_url() ?>';
    </script>

    <!-- jQuery -->
    <script src="<?= base_url('public/vendor/jquery/jquery.min.js') ?>"></script>

    <!-- DataTables -->
    <script src="<?= base_url('public/vendor/datatables/jquery.dataTables.min.js') ?>"></script>
    <script src="<?= base_url('public/vendor/datatables/dataTables.bootstrap4.min.js') ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script>
        const version = new Date().getTime();
        document.querySelectorAll("script").forEach(script => {
            if (script.src) {
                script.src = script.src.split("?")[0] + "?v=" + version;
            }
        });
        function formatearFecha(fecha) {
            let momentFecha = moment(fecha); // Convierte la fecha al formato moment

            // Compara la fecha con 'hoy', 'ayer' o 'mañana'
            let fechaFormateada = momentFecha.isSame(moment(), 'day') ?
                'Hoy ' + momentFecha.fromNow() : // Si es hoy, muestra algo como "Hace 10 minutos"
                momentFecha.isSame(moment().subtract(1, 'days'), 'day') ? 'Ayer' :
                momentFecha.isSame(moment().add(1, 'days'), 'day') ? 'Mañana' :
                momentFecha.format('DD-MM-YYYY'); // Si no es hoy, ayer o mañana, mostramos la fecha en formato

            return fechaFormateada; // Retorna la fecha formateada
        }
    </script>
    <style>
        .icono-solicitud {
            cursor: pointer;
            color: #4e73df;
        }
    </style>
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">