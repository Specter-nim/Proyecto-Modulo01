<?php
namespace App\Services\Statistics;

use Illuminate\Support\Facades\DB;
class StatisticsService
{
    public function getStatistics($start, $end)
    {
        return [
            'terapeutas' => $this->getRendimientoTerapeutas($start, $end),
            'tipos_pago' => $this->getTiposDePago($start, $end),
            'metricas' => $this->getMetricasPrincipales($start, $end),
            'ingresos' => $this->getIngresosPorDiaSemana($start, $end),
            'sesiones' => $this->getSesionesPorDiaSemana($start, $end),
            'tipos_pacientes' => $this->getTiposPacientes($start, $end),
        ];
    }

    /**
     * Obtiene el rendimiento de cada terapeuta en un rango de fechas.
     * 
     * @param string $start Fecha de inicio en formato 'Y-m-d'.
     * @param string $end Fecha de fin en formato 'Y-m-d'.
     * 
     * @return array[] Listado de terapeutas con sus respectivos datos:
     *                 - id: Identificador único de la terapeuta.
     *                 - terapeuta: Nombre completo de la terapeuta.
     *                 - sesiones: Número de citas realizadas en el rango de fechas.
     *                 - ingresos: Monto total de ganancias en el rango de fechas.
     *                 - raiting: Calificación del rendimiento de la terapeuta en una escala de 5 puntos.
     */
    public function getRendimientoTerapeutas($start, $end)
    {
        // 1. Consulta base: sesiones e ingresos por terapeuta
        $stats = DB::table('appointments as a')
            ->join('therapists as t', 'a.therapist_id', '=', 't.id')
            ->selectRaw(<<<'SQL'
                t.id,
                CONCAT(t.paternal_lastname, ' ', t.maternal_lastname, ', ', t.name) as terapeuta,
                COUNT(*) as sesiones,
                SUM(a.payment) as ingresos
            SQL)
            ->whereBetween('a.appointment_date', [$start, $end])
            ->groupBy('t.id', 't.paternal_lastname', 't.maternal_lastname', 't.name')
            ->get();

        // 2. Calculamos promedios globales
        $promSesiones = $stats->avg('sesiones') ?: 1;
        $promIngresos  = $stats->avg('ingresos')  ?: 1;

        // 3. Añadimos el rating original a cada fila
        $withOriginal = $stats->map(function ($row) use ($promSesiones, $promIngresos) {
            $row->raiting_original = ($row->sesiones / $promSesiones) * 0.7
                                + ($row->ingresos  / $promIngresos ) * 0.3;
            return (array) $row;
        });

        // 4. Determinamos el máximo rating original
        $maxOriginal = collect($withOriginal)->max('raiting_original') ?: 1;

        // 5. Escalamos cada rating a 5 puntos y devolvemos el array final
        return collect($withOriginal)
            ->map(function ($item) use ($maxOriginal) {
                $scaled = ($item['raiting_original'] / $maxOriginal) * 5;
                return [
                    'id'        => $item['id'],
                    'terapeuta' => $item['terapeuta'],
                    'sesiones'  => (int) $item['sesiones'],
                    'ingresos'  => (float) $item['ingresos'],
                    'raiting'   => round($scaled, 2),
                ];
            })
            ->values()
            ->all();
    }
    private function getTiposDePago($start, $end)
    {
        return DB::table('appointments as a')
            ->join('payment_types as pt', 'a.payment_type_id', '=', 'pt.id')
            ->select('pt.name')
            ->selectRaw('COUNT(*) as usos')
            ->whereBetween('a.appointment_date', [$start, $end])
            ->groupBy('pt.name')
            ->pluck('usos', 'pt.name')
            ->toArray();
    }
    private function getMetricasPrincipales($start, $end)
    {
        return DB::table('appointments')
            ->selectRaw('
                COUNT(DISTINCT patient_id) as ttlpacientes,
                COUNT(*) as ttlsesiones,
                SUM(payment) as ttlganancias
            ')
            ->whereBetween('appointment_date', [$start, $end])
            ->first();
    }
    private function getIngresosPorDiaSemana($start, $end)
    {
        return DB::table('appointments')
            ->selectRaw('
                DAYNAME(appointment_date) as dia,
                SUM(payment) as total
            ')
            ->whereBetween('appointment_date', [$start, $end])
            ->groupBy('dia')
            ->pluck('total', 'dia');
    }
    private function getSesionesPorDiaSemana($start, $end)
    {
        return DB::table('appointments')
            ->selectRaw('
                DAYNAME(appointment_date) as dia,
                COUNT(*) as sesiones
            ')
            ->whereBetween('appointment_date', [$start, $end])
            ->groupBy('dia')
            ->pluck('sesiones', 'dia');
    }
    private function getTiposPacientes($start, $end)
    {
        return DB::table('appointments')
            ->selectRaw('
                SUM(CASE WHEN BINARY UPPER(TRIM(appointment_type)) = "C" THEN 1 ELSE 0 END) as c,
                SUM(CASE WHEN BINARY UPPER(TRIM(appointment_type)) = "CC" THEN 1 ELSE 0 END) as cc
            ')
            ->whereBetween('appointment_date', [$start, $end])
            ->first();
    }
}