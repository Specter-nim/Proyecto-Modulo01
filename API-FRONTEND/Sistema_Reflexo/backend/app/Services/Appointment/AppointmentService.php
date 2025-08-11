<?php

namespace App\Services\Appointment;

use App\Models\Appointment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use App\Services\Ticket\TicketService;
use Carbon\Carbon;

class AppointmentService
{

    /**
     * Constructor que inyecta el servicio de tickets.
     *
     * @param TicketService $ticketService Servicio para manejar la generación de números de ticket y sala.
     */
    public function __construct(private TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    /**
     * Busca citas por el nombre o apellidos o dni del paciente con paginación.
     *
     * @param array $request Parámetros de búsqueda y paginación ('per_page', 'search').
     * @return JsonResponse Lista paginada de citas o mensaje si no se encuentra ninguna.
     */

    public function searchAppointments(array $request): JsonResponse
    {
        $perPage = $request['per_page'] ?? 30;
        $searchTerm = $request['search'] ?? '';

        $appointments = Appointment::with(['patient', 'paymentType'])
            ->whereHas('patient', function ($query) use ($searchTerm) {
                $this->searchByTerm($query, $searchTerm);
            })
            ->orderByDesc('appointment_date')
            ->paginate($perPage)
            ->appends([
                'per_page' => $perPage,
                'search' => $searchTerm,
            ]);

        if ($appointments->isEmpty()) {
            return response()->json(
                ['message' => 'No se encontraron citas con el término de búsqueda proporcionado'],
                Response::HTTP_OK
            );
        }

        return response()->json($appointments, Response::HTTP_OK);
    }

    /**
     * Busca citas completadas por el nombre o apellidos o dni del paciente con paginación.
     *
     * Una cita se considera completada si tiene:
     * - appointment_status_id = 2
     * - appointment_date no nulo
     * - pago registrado
     *
     * @param array $request Parámetros de búsqueda y paginación ('per_page', 'search').
     * @return JsonResponse Lista de citas completadas o mensaje si no se encuentra ninguna.
     */
    public function searchCompletedAppointments(array $request): JsonResponse
    {
        $perPage = $request['per_page'] ?? 30;
        $searchTerm = $request['search'] ?? '';

        $appointments = Appointment::with(['patient', 'paymentType','therapist'])
            ->whereHas('patient', function ($query) use ($searchTerm) {
                $this->searchByTerm($query, $searchTerm);
            })
            ->where('appointment_status_id', 2)
            ->whereNotNull('appointment_date')
            ->whereNotNull('appointment_status_id')
            ->whereNotNull('payment')
            ->orderByDesc('appointment_date')
            ->paginate($perPage)
            ->appends([
                'per_page' => $perPage,
                'search' => $searchTerm,
            ]);

        if ($appointments->isEmpty()) {
            return response()->json(
                ['message' => 'No se encontraron citas con el término de búsqueda proporcionadaa'],
                Response::HTTP_OK
            );
        }

        return response()->json($appointments, Response::HTTP_OK);
    }

    /**
     * Obtiene todas las citas con paginación con filtro por fecha.
     *
     * @param array $request Parámetros: 'per_page' y 'date' (fecha de la cita).
     * @return JsonResponse Citas filtradas por fecha o mensaje si no hay ninguna.
     */
    public function getPaginatedAppointmentsByDate(array $request): JsonResponse
    {

        $date = $request['date'] ?? null;
        $perPage = $request['per_page'] ?? 30;

        // Inicialización de consulta con relación 'patient'
        $query = Appointment::with(['patient', 'paymentType']);

        if ($date) {
            $query->whereDate('appointment_date', $date)
                ->orderByDesc('ticket_number');
        }

        $appointments = $query->paginate($perPage)->appends([
            'per_page' => $perPage,
            'date' => $date
        ]);

        if ($appointments->isEmpty()) {
            return response()->json(
                ['message' => 'Aún no se ha creado ninguna cita'],
                Response::HTTP_OK
            );
        }

        return response()->json($appointments, Response::HTTP_OK);
    }

    /**
     * Obtiene citas completadas por fecha con paginación.
     *
     * Una cita completada incluye:
     * - appointment_status_id = 2
     * - appointment_date, appointment_status_id y payment no nulos.
     *
     * @param array $request Parámetros: 'per_page' y 'date'.
     * @return JsonResponse Lista de citas completadas o mensaje si no se encuentra ninguna.
     */
    public function getCompletedAppointmentsPaginatedByDate(array $request): JsonResponse
    {
        $date = $request['date'] ?? null;
        $perPage = $request['per_page'] ?? 30;

        
        // Consulta con relación 'patient'
        $query = Appointment::with(['patient', 'paymentType','therapist']);

        if ($date) {
            $query->whereDate('appointment_date', $date)
                ->where('appointment_status_id', 2)
                ->orderByDesc('ticket_number')
                ->whereNotNull('appointment_date')
                ->whereNotNull('appointment_status_id')
                ->whereNotNull('payment');
        }

        $appointments = $query->paginate($perPage)->appends([
            'per_page' => $perPage,
            'date' => $date
        ]);

        if ($appointments->isEmpty()) {
            return response()->json(
                ['message' => 'Aún no se ha completado ninguna cita este día'],
                Response::HTTP_OK
            );
        }

        return response()->json($appointments, Response::HTTP_OK);
    }

    /**
     * Obtiene citas pendientes para mostrar en un calendario, filtradas por rango de fechas.
     *
     * Solo incluye citas:
     * - con appointment_status_id = 1
     * - con fecha mayor o igual a hoy
     *
     * @return JsonResponse Lista de citas pendientes o mensaje si no se encuentra ninguna.
     */
    public function getPendingAppointmentsForCalendarByDate(): JsonResponse
    {
        $todayDate = Carbon::today('America/Lima')->toDateString();

        $appointments = Appointment::with(['patient', 'paymentType'])
            ->where('appointment_status_id', 1)
            ->whereDate('appointment_date', '>=', $todayDate)
            ->orderBy('appointment_date')
            ->orderBy('appointment_hour')
            ->get();

        if ($appointments->isEmpty()) {
            return response()->json(
                ['message' => 'No hay citas pendientes'],
                Response::HTTP_OK
            );
        }

        // Agregar 'full_name' desde el paciente
        $appointments = $appointments->map(function ($appointment) {
            $data = $appointment->toArray();

            if ($appointment->patient) {
                $data['full_name'] = "{$appointment->patient->name} {$appointment->patient->paternal_lastname} {$appointment->patient->maternal_lastname}";
            } else {
                $data['full_name'] = null;
            }

            return $data;
        });

        return response()->json($appointments, Response::HTTP_OK);
    }

    /**
     * Obtiene citas completadas para mostrar en un calendario, filtradas por rango de fechas.
     *
     * Solo incluye citas:
     * - con appointment_status_id = 2
     * - con payment_type_id y payment no nulos.
     *
     * @param array $request Parámetros: 'startDate', 'endDate', 'per_page'.
     * @return JsonResponse Lista de citas completadas o mensaje si no se encuentra ninguna.
     */
    public function getCompletedAppointmentsForCalendarByDate(array $request): JsonResponse
    {
        $startDate = $request['startDate'] ?? null;
        $endDate = $request['endDate'] ?? null;
        $perPage = $request['per_page'] ?? 30;


        $query = Appointment::with(['patient', 'paymentType','therapist']);

        if ($startDate != null && $endDate != null) {
            $query->whereBetween('appointment_date', [$startDate, $endDate])
                ->where('appointment_status_id', 2)
                ->whereNotNull('payment_type_id')
                ->whereNotNull('payment')
                ->orderBy('appointment_date');
        }

        $appointments = $query->paginate($perPage)->appends([
            'per_page' => $perPage,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);

        if ($appointments->isEmpty()) {
            return response()->json(
                ['message' => 'Aún no se ha completado ninguna cita para este día'],
                Response::HTTP_OK
            );
        }

        return response()->json($appointments, Response::HTTP_OK);
    }


        /**
         * Almacenar una nueva cita o restaurar una eliminada
         */
        public function storeOrRestore(array $data): JsonResponse
        {
            $appointment = $this->findTrashedOrExisting($data);

            if ($appointment) {
                if ($appointment->trashed()) {
                    // Restaurar la cita y regenerar números de sala y ticket
                    $appointment->restore();

                    // Regenerar número de sala
                    $appointment->room = $this->ticketService->generateNextRoomNumber($appointment->appointment_date);

                    // Regenerar número de ticket solo si tiene pago
                    if ($appointment->payment !== null) {
                        $appointment->ticket_number = $this->ticketService->generateNextTicketNumber($appointment->appointment_date);
                    }

                    $appointment->save();

                    return $this->respondRestored($appointment);
                }

                return $this->respondConflict();
            }

            // Autocompletado si existe una cita anterior
            $data = $this->fillFromPreviousAppointment($data);

            // Generate room number if not provided
            if (!isset($data['room'])) {
                $data['room'] = $this->ticketService->generateNextRoomNumber($data['appointment_date']);
            }

            // Generate ticket number if payment is set
            if (isset($data['payment']) && $data['payment'] !== null && !isset($data['ticket_number'])) {
                $data['ticket_number'] = $this->ticketService->generateNextTicketNumber($data['appointment_date']);
            }

            // Crear nueva cita
            $newAppointment = Appointment::create($data);


            // Verificar si la fecha de la cita es anterior a hoy (zona horaria de Perú)
            $peruToday = Carbon::now('America/Lima')->startOfDay();
            $appointmentDate = Carbon::parse($data['appointment_date'])->startOfDay();

            $response = ['data' => $newAppointment];

            $response = [
                'message' => 'La cita fue creada exitosamente',
                'data' => $newAppointment->fresh(['patient', 'paymentType'])
            ];

            if ($appointmentDate->isBefore($peruToday)) {
                $response['warning'] = 'La cita fue creada con una fecha anterior al día de hoy';
            }

            return response()->json($response, Response::HTTP_CREATED);
        }

    /**
     * Actualizar datos de una cita
     */
    public function update(Appointment $appointment, array $data): JsonResponse
    {
        // Al inicio del método update
        if (isset($data['appointment_date']) && empty($data['appointment_date'])) {
            unset($data['appointment_date']);
        }

        if ($this->appointmentExists($appointment, $data)) {
            return $this->respondConflict();
        }

        // Handle date change - regenerate room and ticket numbers
        if (isset($data['appointment_date']) && $data['appointment_date'] != $appointment->appointment_date) {
            $data['room'] = $this->ticketService->generateNextRoomNumber(
                $data['appointment_date'],
                $appointment->id
            );

            // Only regenerate ticket if had payment
            if ($appointment->payment !== null) {
                $data['ticket_number'] = $this->ticketService->generateNextTicketNumber(
                    $data['appointment_date'],
                    $appointment->id
                );
            }
        }

        // Handle payment being set - generate ticket number if needed
        if (isset($data['payment'])) {
            if ($data['payment'] === null && $appointment->payment !== null) {
                // Payment removed - release ticket
                $data['ticket_number'] = null;
            } elseif ($data['payment'] !== null && $appointment->payment === null) {
                // Payment added - generate ticket
                $data['ticket_number'] = $this->ticketService->generateNextTicketNumber(
                    $data['appointment_date'] ?? $appointment->appointment_date,
                    $appointment->id
                );
            }
        }

        $appointment->update($data);
        return response()->json($appointment->load(['patient.history', 'paymentType']));
    }

    /**
     * Eliminar cita
     */
    public function destroy(Appointment $appointment): JsonResponse
    {

        $appointment->update([
            'room' => null,
            'ticket_number' => null,
        ]);

        $appointment->delete();
        return response()->json(['message' => 'La cita se ha borrado correctamente'], Response::HTTP_OK);
    }

    // =====================
    // MÉTODOS PRIVADOS
    // =====================


    /**
     * Busca una cita eliminada o existente para un paciente y fecha específicos.
     *
     * Este método busca una cita en la base de datos que coincida con el
     * 'patient_id', 'appointment_date' y, opcionalmente, 'appointment_hour'
     * proporcionados en el array de datos. Incluye las citas eliminadas
     * (soft-deleted) en la búsqueda.
     *
     * @param array $data Contiene 'patient_id', 'appointment_date' y opcionalmente 'appointment_hour'.
     * @return Appointment|null Devuelve la cita encontrada o null si no existe.
     */

    private function findTrashedOrExisting(array $data): ?Appointment
    {
        $query = Appointment::withTrashed()
            ->where('patient_id', $data['patient_id'])
            ->where('appointment_date', $data['appointment_date']);

        if (!empty($data['appointment_hour'])) {
            $query->where('appointment_hour', $data['appointment_hour']);
        }

        return $query->first();
    }


    /**
     * Autocompleta campos de la cita con los datos de la cita completa previa del paciente.
     *
     * Si no existe una cita previa, se devuelve el array original sin cambios.
     *
     * @param array $data Los datos de la cita a autocompletar
     * @return array Los datos de la cita autocompletados
     */
    private function fillFromPreviousAppointment(array $data): array
    {
        $previousAppointment = $this->getLastCompleteAppointment($data['patient_id']);

        if (!$previousAppointment) return $data;

        // Lista de campos que podrían autocompletarse
        $fieldsToFill = [
            'diagnosis',
            'ailments',
            'surgeries',
            'reflexology_diagnostics',
            'diagnosis',
            'ailments',
            'surgeries',
            'reflexology_diagnostics',
            'medications',
            'observation',
            'initial_date',
            'history_id'
        ];

        foreach ($fieldsToFill as $field) {
            if (empty($data[$field]) && !empty($previousAppointment->$field)) {
                $data[$field] = $previousAppointment->$field;
            }
        }

        return $data;
    }


    /**
     * Obtiene la última cita completa del paciente.
     *
     * Se considera una cita completa si tiene al menos uno de los siguientes campos:
     * - diagnosis
     * - surgeries
     * - reflexology_diagnostics
     * - medications
     * - observation
     * - initial_date
     * - final_date
     * - appointment_type
     * - social_benefit
     * - payment_detail
     * - payment
     * - appointment_status_id
     * - payment_type_id
     * - history_id
     * - therapist_id
     *
     * Se ordenan las citas por fecha y hora descendente y se devuelve la primera
     * que tenga al menos un campo no vacío.
     *
     * @param int $patientId Identificador del paciente.
     * @return Appointment|null La última cita completa del paciente o null si no existe.
     */
    private function getLastCompleteAppointment(int $patientId): ?Appointment
    {
        return Appointment::where('patient_id', $patientId)
            ->orderByDesc('appointment_date')
            ->orderByDesc('appointment_hour')
            ->get()
            ->sortByDesc(function ($appointment) {
                return collect([
                    $appointment->diagnosis,
                    $appointment->surgeries,
                    $appointment->reflexology_diagnostics,
                    $appointment->medications,
                    $appointment->observation,
                    $appointment->initial_date,
                    $appointment->final_date,
                    $appointment->appointment_type,
                    $appointment->social_benefit,
                    $appointment->payment_detail,
                    $appointment->payment,
                    $appointment->appointment_status_id,
                    $appointment->payment_type_id,
                    $appointment->history_id,
                    $appointment->therapist_id
                ])->filter()->count(); // Devuelve la cantidad de campos no vacíos
            })->first();
    }

    /**
     * Verifica si ya existe otra cita en el mismo horario
     */
    private function appointmentExists(Appointment $appointment, array $data): bool
    {
        $query = Appointment::withTrashed()
            ->where('patient_id', $data['patient_id'])
            ->where('appointment_date', $data['appointment_date'])
            ->where('id', '!=', $appointment->id);

        if (!empty($data['appointment_hour'])) {
            $query->where('appointment_hour', $data['appointment_hour']);
        }

        return $query->exists();
    }

    /**
     * Respuesta para citas ya existentes
     */
    private function respondConflict(): JsonResponse
    {
        return response()->json([
            'message' => 'La cita de este paciente y con este horario ya existe'
        ], Response::HTTP_CONFLICT);
    }

    /**
     * Respuesta para citas restauradas exitosamente
     */
    private function respondRestored(Appointment $appointment): JsonResponse
    {
        return response()->json([
            'message' => 'La cita fue restaurada',
            'data' => $appointment->fresh()
        ], Response::HTTP_OK);
    }

    /**
     * Aplica múltiples condiciones de búsqueda sobre un paciente con base en un término proporcionado.
     * 
     * Se consideran coincidencias exactas o parciales con el número de documento, nombres, apellidos,
     * y varias combinaciones posibles de nombre completo (primer y segundo nombre con apellidos, etc.).
     *
     * @param Builder $query Consulta del modelo relacionada con el paciente.
     * @param string $searchTerm Término de búsqueda que puede ser número de documento, nombre o apellidos.
     * @return Builder Consulta modificada con las condiciones de búsqueda aplicadas.
     */
    private function searchByTerm(Builder $query, string $searchTerm): Builder
    {
        return $query->where(function ($q) use ($searchTerm) {
            $q->where('document_number', '=', $searchTerm)
                ->orWhere('document_number', 'LIKE', "{$searchTerm}%")

                // Primer o segundo nombre que comiencen con searchTerm
                ->orWhere('name', 'LIKE', "{$searchTerm}%")
                ->orWhere('name', 'LIKE', "% {$searchTerm}%")

                // Apellidos que comiencen con searchTerm
                ->orWhere('paternal_lastname', 'LIKE', "{$searchTerm}%")
                ->orWhere('paternal_lastname', 'LIKE', "% {$searchTerm}%")
                ->orWhere('maternal_lastname', 'LIKE', "{$searchTerm}%")
                ->orWhere('maternal_lastname', 'LIKE', "% {$searchTerm}%")

                // Búsqueda por nombre completo: "Apellido Nombre"
                ->orWhereRaw("CONCAT(paternal_lastname, ' ', name) LIKE ?", ["{$searchTerm}%"])

                // Búsqueda por nombre completo: "Apellido Paterno, Apellido Materno ,Nombre"
                ->orWhereRaw("CONCAT(paternal_lastname, ' ', maternal_lastname, ' ', name) LIKE ?", ["{$searchTerm}%"])

                // Búsqueda por nombre completo: "Nombre, Apellido Paterno, Apellido Materno"
                ->orWhereRaw("CONCAT(name, ' ', paternal_lastname, ' ', maternal_lastname) LIKE ?", ["{$searchTerm}%"])

                // Búsqueda considerando primer y segundo nombre separados, primer nombre con apellidos completos y segundo o ultimo nombre con apellidos completos
                ->orWhereRaw("
                    CONCAT(
                        COALESCE(paternal_lastname, ''), ' ',
                        COALESCE(SUBSTRING_INDEX(name, ' ', 1), ''), ' ',
                        COALESCE(SUBSTRING_INDEX(name, ' ', -1), '')
                    ) LIKE ?
                ", ["{$searchTerm}%"])

                ->orWhereRaw("
                    CONCAT(
                        COALESCE(maternal_lastname, ''), ' ',
                        COALESCE(SUBSTRING_INDEX(name, ' ', 1), ''), ' ',
                        COALESCE(SUBSTRING_INDEX(name, ' ', -1), '')
                    ) LIKE ?
                ", ["{$searchTerm}%"])

                ->orWhereRaw("
                    CONCAT(
                        COALESCE(SUBSTRING_INDEX(name, ' ', 1), ''), ' ',
                        COALESCE(paternal_lastname, ''), ' ',
                        COALESCE(maternal_lastname, '')
                    ) LIKE ?
                ", ["{$searchTerm}%"])

                ->orWhereRaw("
                    CONCAT(
                        COALESCE(SUBSTRING_INDEX(name, ' ', -1), ''), ' ',
                        COALESCE(paternal_lastname, ''), ' ',
                        COALESCE(maternal_lastname, '')
                    ) LIKE ?
                ", ["{$searchTerm}%"]);
        });
    }
}
