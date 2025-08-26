<?php

namespace App\Controllers;

class ConvertidoresController extends BaseController
{
    public function convertirFechaATexto($fecha)
    {
        $dateTime = new \DateTime($fecha);
        $dias = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre'
        ];

        $dia = $dateTime->format('j'); // Día del mes sin ceros a la izquierda
        $mes = $dias[(int)$dateTime->format('n')]; // Mes en texto
        $anio = $dateTime->format('Y'); // Año

        $fechaTxt = "{$dia} de {$mes} de {$anio}";

        return strtoupper($fechaTxt);
    }

    public function calcularEdadEnLetras($fechaNacimiento): String
    {
        $nacimiento = new \DateTime($fechaNacimiento);
        $hoy = new \DateTime();

        $edad = $hoy->diff($nacimiento)->y;
        $fmt = new \NumberFormatter('es', \NumberFormatter::SPELLOUT);
        $edadLetras = $fmt->format($edad);

        $edadLetrasMayusculas = strtoupper($edadLetras);
        return $edadLetrasMayusculas;
    }

    public function convertirNumeroALetras($numero): string
    {
        // Crea el objeto NumberFormatter para español
        $formatter = new \NumberFormatter('es', \NumberFormatter::SPELLOUT);

        // Formatea el número
        $numeroEnLetras = $formatter->format($numero);

        // Retornar en mayúsculas si deseas
        return strtoupper($numeroEnLetras);
    }
}
