<!DOCTYPE html>
<html lang="">

<head>
    <title>Reporte</title>
    <meta content="summary_large_image" name="twitter:card" />
    <meta content="website" property="og:type" />
    <meta content="" property="og:description" />
    <meta content="https://8idsxi3l9e.preview-beefreedesign.com/nTrt" property="og:url" />
    <meta content="https://pro-bee-beepro-thumbnail.getbee.io/messages/1217305/1203286/2192228/11232418_large.jpg" property="og:image" />
    <meta content="" property="og:title" />
    <meta content="" name="description" />
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #000000;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }

        .bee-page-container {
            padding: 20px;
        }

        h1,
        h3 {
            margin: 0;
        }

        .title {
            color: #000000;
            font-size: 28px;
            font-weight: 700;
            text-align: center;
            margin-top: 0;
            margin-bottom: 20px;
        }

        .header-table {
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 10px;
        }

        .header-table td {
            padding: 5px 0;
            vertical-align: top;
        }

        .header-left {
            text-align: left;
            width: 50%;
            font-size: 12px;
        }

        .header-right {
            text-align: right;
            width: 50%;
            font-size: 12px;
        }

        .bee-table {
            margin-top: 20px;
        }

        .bee-table table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 12px;
        }

        .bee-table th,
        .bee-table td {
            padding: 10px;
            border: 1px solid #dddddd;
            word-break: break-word;
        }

        .bee-table th {
            background-color: #e0e0e0;
            text-align: left;
            font-weight: 700;
        }

        .bee-table td {
            text-align: left;
        }

        .bee-table .codigo {
            width: 12%;
        }

        .bee-table .nombre {
            width: 40%;
        }

        .bee-table .cantidad,
        .bee-table .precio {
            width: 16.6%;
            text-align: center;
        }

        .bee-table .total {
            width: 16.6%;
            text-align: right;
        }

        .total {
            text-align: right;
            font-size: 12px;
            font-weight: 700;
            margin-top: 10px;
        }

        @media print {
            thead {
                display: table-header-group;
            }
        }
    </style>
</head>

<body>
    <div class="bee-page-container">
        <h1 class="title">Ingresos por compras</h1><br><br>
        <table class="header-table">
            <tr>
                <td class="header-left">
                    <h3>Correlativo: <?php echo $documento['correlativo']; ?></h3>
                </td>
                <td class="header-right">
                    <h3>Fecha: <?php echo $documento['fecha_creacion']; ?></h3>
                </td>
            </tr>
            <tr>
                <td class="header-left">
                    <h3>No. Documento: <?php echo $documento['noDocumento']; ?></h3>
                </td>
                <td class="header-right">
                    <h3>Estatus: <?php echo $documento['estado']; ?></h3>
                </td>
            </tr>
            <tr>
                <td class="header-left">
                    <h3>Sucursal: <?php echo $documento['sucursal']; ?></h3>
                </td>
                <td class="header-right"></td>
            </tr>
            <tr>
                <td class="header-left">
                    <h3>Proveedor: <?php echo $documento['proveedor']; ?></h3>
                </td>
                <td class="header-right"></td>
            </tr>
            <tr>
                <td class="header-left" colspan="2">
                    <h3>Observación: <?php echo $documento['observaciones']; ?></h3>
                </td>
            </tr>
        </table>

        <div class="bee-table">
            <table>
                <thead>
                    <tr>
                        <th class="codigo">CODIGO</th>
                        <th class="nombre">NOMBRE</th>
                        <th class="cantidad">CANTIDAD</th>
                        <th class="precio">PRECIO</th>
                        <th class="total">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $producto) : ?>
                        <tr>
                            <td class="codigo"><?php echo $producto['codigo_producto']; ?></td>
                            <td class="nombre"><?php echo $producto['nombre'] . ' ' . $producto['modelo']; ?></td>
                            <td class="cantidad"><?php echo $producto['cantidad']; ?></td>
                            <td class="precio">$<?php echo $producto['precio']; ?></td>
                            <td class="total">$<?php echo $producto['cantidad'] * $producto['precio']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="total">
            <h3>TOTAL: $<?php echo $documento['monto_total']; ?></h3>
        </div>
    </div>
</body>

</html>