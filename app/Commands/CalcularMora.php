<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\Database\BaseConnection;

class CalcularMora extends BaseCommand
{
    protected $group       = 'Custom';
    protected $name        = 'mora:calcular';
    protected $description = 'Calcula y actualiza la mora diaria de los cobros vencidos';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('cobros');

        // Parámetros
        $interestRate = 0.07; // 7% mensual
        $daysInMonth = 30;
        $today = new \DateTime();

        // Buscar cobros vencidos sin pagar completamente
        $cobros = $builder
            ->select('*')
            ->where('estado', 'PENDIENTE')
            ->where('fecha_vencimiento <', $today->format('Y-m-d'))
            ->get()
            ->getResult();

        foreach ($cobros as $cobro) {
            $fechaVencimiento = new \DateTime($cobro->fecha_vencimiento);
            $fechaVencimiento->setTime(0, 0, 0);

            $diff = $today->diff($fechaVencimiento);
            $diasAtraso = $diff->invert ? $diff->days : 0;

            if ($diasAtraso > 0) {
                $montoCuota = floatval($cobro->monto_cuota);
                $moraDiaria = ($montoCuota * $interestRate) / $daysInMonth;
                $mora = round($moraDiaria * $diasAtraso, 2); // Redondeo a dos decimales

                // Actualizar interesGenerado
                $builder->where('id_cobro', $cobro->id_cobro)
                        ->update(['interesGenerado' => $mora]);

                CLI::write("Cobro #{$cobro->id_cobro}: Mora acumulada = $mora", 'green');
                log_message("info","Cobro #{$cobro->id_cobro}: Mora acumulada = $mora");
            }
        }
        log_message("info","✅ Mora calculada correctamente para los cobros vencidos.");
        CLI::write('✅ Mora calculada correctamente para los cobros vencidos.', 'yellow');
    }
}
