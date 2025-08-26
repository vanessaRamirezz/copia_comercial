<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Factura</title>
  <style>
    * {
      font-family: Arial, sans-serif !important;
      font-size: 12px !important;
      /* antes era 14px */
    }

    table {
      width: 100%;
      border-collapse: collapse !important;
    }

    td,
    th {
      border: 1px dotted #000 !important;
      padding: 5px !important;
      text-align: center;
      vertical-align: middle;
    }

    .no-border {
      border: none !important;
    }

    .encabezado {
      margin-bottom: 20px;
    }

    .alinear-izquierda {
      text-align: left !important;
    }

    .alinear-derecha {
      text-align: right !important;
    }

    .totales td {
      font-weight: bold;
    }
  </style>
</head>

<body>

  <table class="encabezado">
    <tr>
      <td class="no-border alinear-izquierda" style="width: 70%;">
        <strong>TODO PARA EL HOGAR</strong><br>
        E-MAIL: <a href="mailto:comercialtph@hotmail.com">comercialtph@hotmail.com</a><br><br>
        Nombre cliente: <?= esc($nombreCliente) ?><br>
        Detalles N° series : <?= esc($detalleSeries) ?><br><?= esc($productosVenta) ?>
      </td>
      <td class="no-border alinear-derecha" style="width: 30%;">
        <strong>FACTURA</strong><br>
        No. <?= esc($noContrato) ?><br>
        <?= esc($fecha) ?>
      </td>
    </tr>
  </table>

  <table>
    <thead>
      <tr>
        <th style="width: 15%;">CÓDIGO</th>
        <th style="width: 35%;">DESCRIPCIÓN</th>
        <th style="width: 10%;">CANT</th>
        <th style="width: 20%;">PRECIO UNITARIO</th>
        <th style="width: 20%;">TOTAL</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($pagos as $pago): ?>
        <tr>
          <td><?= esc($codigo ?? '') ?></td>
          <td><?= esc(ucfirst(mb_strtolower($pago['descripcion'], 'UTF-8'))) ?></td>
          <td>1</td>
          <td>$<?= number_format($pago['abono'], 2) ?></td>
          <td>$<?= number_format($pago['abono'], 2) ?></td>
        </tr>
      <?php endforeach; ?>
      <tr class="totales">
        <td colspan="4" class="alinear-derecha">Total</td>
        <td><?= esc($sumaTotal) ?></td>
      </tr>
    </tbody>
  </table>

  <p style="margin-top: 20px;">
    <strong>Total a pagar:</strong> <?= esc($totalApagarLetras) ?>
  </p>

  <p style="margin-top: 20px;">
    <strong>Saldo anterior:</strong> $<?= esc($saldoAnterior) ?><br>
    <strong>Saldo actual:</strong> $<?= esc($saldoActual) ?>
  </p>

  <p style="margin-top: 30px;">
    VISÍTENOS Y SERÁ AMABLEMENTE ATENDIDO POR NUESTRO PERSONAL
  </p>

</body>

</html>