<?php

namespace App\Services\Report;

use App\Models\Appointment;
use App\Models\PaymentType;
use App\Models\Therapist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Obtiene la cantidad de citas por terapeuta para una fecha específica.
     *
     * @param Request $request Parámetro de consulta 'date' (formato 'Y-m-d'), opcional.
     * @return JsonResponse Lista de terapeutas con número de citas y total acumulado.
     */
    public function getAppointmentsCountByTherapist(Request $request): JsonResponse
    {
        $date = $request->query('date');

        if ($date && !Carbon::hasFormat($date, 'Y-m-d')) {
            return response()->json(['error' => 'Formato de fecha inválido. Use YYYY-mm-dd.'], 422);
        }

        $date = $date ?? Carbon::now('America/Lima')->toDateString();

        $therapists = Therapist::withCount([
            'appointments' => function ($query) use ($date) {
                $query->whereDate('appointment_date', $date);
            }
        ])
        ->having('appointments_count', '>', 0)
        ->get(['id', 'name', 'paternal_lastname', 'maternal_lastname']);

        $totalAppointments = $therapists->sum('appointments_count');

        $data = $therapists->map(function ($therapist) {
            return [
                'id' => $therapist->id,
                'name' => $therapist->name,
                'paternal_lastname' => $therapist->paternal_lastname,
                'maternal_lastname' => $therapist->maternal_lastname,
                'appointments_count' => $therapist->appointments_count,
            ];
        });

        return response()->json([
            'therapists_appointments' => $data,
            'total_appointments_count' => $totalAppointments,
        ]);
    }

    /**
     * Retorna los pacientes agrupados por terapeuta según las citas registradas en una fecha dada.
     *
     * @param Request $request Parámetro de consulta 'date' (formato 'Y-m-d'), obligatorio.
     * @return JsonResponse Arreglo de terapeutas con sus respectivos pacientes y número de citas.
     */
    public function getPatientsByTherapist(Request $request): JsonResponse
    {
        $date = $request->query('date');
    
        if (!Carbon::hasFormat($date, 'Y-m-d')) {
            return response()->json(['error' => 'Formato de fecha inválido. Use YYYY-MM-DD.'], 422);
        }
    
        // Obtener citas con relaciones
        $appointments = Appointment::with(['patient', 'therapist'])
            ->whereDate('appointment_date', $date)
            ->whereNull('deleted_at')
            ->get();
    
        $report = [];
        $sinTerapeuta = [
            'therapist_id' => '',
            'therapist' => 'Sin terapeuta asignado',
            'patients' => []
        ];
    
        foreach ($appointments as $appointment) {
            $therapist = $appointment->therapist;
            $patient = $appointment->patient;
    
            if (!$patient) continue;
    
            $patientData = [
                'patient_id' => $patient->id,
                'patient' => trim("{$patient->paternal_lastname} {$patient->maternal_lastname} {$patient->name}"),
                'appointments' => 1
            ];
    
            if (!$therapist) {
                $key = $patient->id;
                if (!isset($sinTerapeuta['patients'][$key])) {
                    $sinTerapeuta['patients'][$key] = $patientData;
                } else {
                    $sinTerapeuta['patients'][$key]['appointments'] += 1;
                }
            } else {
                $idTherapist = $therapist->id;
                if (!isset($report[$idTherapist])) {
                    $report[$idTherapist] = [
                        'therapist_id' => $idTherapist,
                        'therapist' => trim("{$therapist->paternal_lastname} {$therapist->maternal_lastname} {$therapist->name}"),
                        'patients' => []
                    ];
                }
    
                $key = $patient->id;
                if (!isset($report[$idTherapist]['patients'][$key])) {
                    $report[$idTherapist]['patients'][$key] = $patientData;
                } else {
                    $report[$idTherapist]['patients'][$key]['appointments'] += 1;
                }
            }
        }
    
        if (!empty($sinTerapeuta['patients'])) {
            $report['sinTherapist'] = $sinTerapeuta;
        }
    
        
        foreach ($report as &$r) {
            $r['patients'] = array_values($r['patients']);
        }
    
        return response()->json(array_values($report));
    }


    /**
     * Genera un reporte diario de ingresos agrupados por tipo de pago y monto.
     *
     * @param Request $request Parámetro de consulta 'date' (formato 'Y-m-d'), obligatorio.
     * @return JsonResponse Reporte agrupado por tipo de pago y monto, ordenado por prioridad.
     */
    public function getDailyCash(Request $request): JsonResponse
    {
        $date = $request->query('date');

        if (!Carbon::hasFormat($date, 'Y-m-d')) {
            return response()->json(['error' => 'Formato de fecha inválido. Use YYYY-MM-DD.'], 422);
        }
    
        // Obtener los nombres de los tipos de pago válidos desde la tabla payment_types
        $validPaymentTypes = PaymentType::pluck('name')->map(function ($name) {
            return strtoupper(trim($name));
        })->toArray();
    
        $appointments = Appointment::with('paymentType')
            ->whereDate('appointment_date', $date)
            ->whereNull('deleted_at')
            ->get();
            

        $report = [];
    
        foreach ($appointments as $appointment) {
            $paymentTypeName = strtoupper(trim($appointment->paymentType->name ?? ''));
            $paymentAmount = $appointment->payment ?? 0;
    
            // Omitir si el método de pago no es válido
            if (!in_array($paymentTypeName, $validPaymentTypes)) {
                continue;
            }
    
            // Formatear el pago: quitar .00 pero mantener .50 u otros decimales
            $formattedPayment = (fmod($paymentAmount, 1) == 0) ? (int)$paymentAmount : number_format($paymentAmount, 2);
    
            $key = "{$paymentTypeName}_{$formattedPayment}";
    
            if (!isset($report[$key])) {
                $report[$key] = [
                    'name' => "{$paymentTypeName} {$formattedPayment}",
                    'countAppointment' => 0,
                    'payment' => $formattedPayment,
                    'total' => 0
                ];
            }
    
            $report[$key]['countAppointment']++;
            $report[$key]['total'] += $paymentAmount;
        }
    
        // Separar y ordenar los métodos de pago
        $sortedReport = [];
    
        // CUPÓN SIN COSTO primero
        foreach ($report as $key => $item) {
            if (strpos($key, 'CUPÓN SIN COSTO') === 0) {
                $sortedReport[$item['name']] = $item;
                unset($report[$key]);
            }
        }
    
        // EFECTIVO segundo
        $efectivo = [];
        foreach ($report as $key => $item) {
            if (strpos($key, 'EFECTIVO') === 0) {
                $efectivo[] = $item;
                unset($report[$key]);
            }
        }
        usort($efectivo, fn($a, $b) => $b['payment'] <=> $a['payment']);
        foreach ($efectivo as $item) {
            $sortedReport[$item['name']] = $item;
        }
    
        // YAPE tercero
        $yape = [];
        foreach ($report as $key => $item) {
            if (strpos($key, 'YAPE') === 0) {
                $yape[] = $item;
                unset($report[$key]);
            }
        }
        usort($yape, fn($a, $b) => $b['payment'] <=> $a['payment']);
        foreach ($yape as $item) {
            $sortedReport[$item['name']] = $item;
        }
    
        // Otros o nuevos metodos de pago ultimo
        foreach ($report as $item) {
            $sortedReport[$item['name']] = $item;
        }
    
        return response()->json($sortedReport);
    }
    

    /**
     * Devuelve las citas registradas entre dos fechas específicas.
     *
     * @param Request $request Parámetros de consulta 'startDate' y 'endDate' (formato 'Y-m-d'), obligatorios.
     * @return JsonResponse Lista de citas con información del paciente.
     */
    public function getAppointmentsBetweenDates(Request $request): JsonResponse 
    {
        $startDate = $request->query('startDate');
        $endDate = $request->query('endDate');

        if (!Carbon::hasFormat($startDate, 'Y-m-d') || !Carbon::hasFormat($endDate, 'Y-m-d')) {
            return response()->json(['error' => 'Formato de fecha inválido. Use YYYY-MM-DD.'], 422);
        }

        if (Carbon::parse($startDate)->gt(Carbon::parse($endDate))) {
            return response()->json(['error' => 'La fecha de inicio no puede ser mayor que la fecha de fin.'], 422);
        }

        $appointments = Appointment::with('patient')
            ->whereBetween('appointment_date', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->whereNotNull('payment')
            ->whereNotNull('payment_type_id')
            ->get();

        $data = $appointments->map(function ($appointment) {
            $patient = $appointment->patient;

            return [
                'patient_id' => $patient->id,
                'document_number' => $patient->document_number,
                'name' => $patient->name,
                'paternal_lastname' => $patient->paternal_lastname,
                'maternal_lastname' => $patient->maternal_lastname,
                'primary_phone' => $patient->primary_phone,
                'appointment_date' => $appointment->appointment_date,
                'appointment_hour' => $appointment->appointment_hour,
            ];
        });

        return response()->json([
            'appointments' => $data
        ]);
    }


}
