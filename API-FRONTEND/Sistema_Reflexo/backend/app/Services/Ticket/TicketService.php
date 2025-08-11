<?php

namespace App\Services\Ticket;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TicketService
{

    /**
     * Genera el siguiente número de habitación disponible para una fecha determinada.
     *
     * @param string $date La fecha para la que se generará el número de habitación.
     * @param int|null $excludeAppointmentId ID de cita opcional para excluir de la búsqueda.
     * @return int El siguiente número de habitación disponible.
     */
    public function generateNextRoomNumber(string $date, ?int $excludeAppointmentId = null): int
    {
        $dateObj = Carbon::parse($date);
        $isWeekend = $dateObj->isWeekend();
        $maxRooms = $isWeekend ? 24 : 14;

        // Retrieve all occupied rooms for the given date
        // Obtener todas las salas ocupadas para esta fecha
        $query = Appointment::whereDate('appointment_date', $date)
            ->whereNotNull('room')
            ->whereNull('deleted_at');

        if ($excludeAppointmentId) {
            $query->where('id', '!=', $excludeAppointmentId);
        }

        $usedRoomNumbers = $query->pluck('room')->toArray();

        // Check for missing numbers in the sequence
        // Verificar si hay números faltantes en la secuencia
        for ($i = 1; $i <= $maxRooms; $i++) {
            if (!in_array($i, $usedRoomNumbers)) {
                return $i;
            }
        }

        // If all room numbers are used, start a new sequence
        $roomCount = count($usedRoomNumbers);
        return ($roomCount % $maxRooms) + 1;
    }


    /**
     * Genera el siguiente número de ticket disponible para una fecha determinada.
     *
     * @param string $date La fecha para la que se generará el número de ticket.
     * @param int|null $excludeAppointmentId ID de cita opcional para excluir de la búsqueda.
     * @return int El siguiente número de ticket disponible.
     */
    public function generateNextTicketNumber(string $date, ?int $excludeAppointmentId = null): int
    {
        // Filtrar por fecha específica
        $query = Appointment::whereDate('appointment_date', $date)
            ->whereNotNull('ticket_number')
            ->whereNull('deleted_at');

        if ($excludeAppointmentId) {
            $query->where('id', '!=', $excludeAppointmentId);
        }

        $usedTickets = $query->pluck('ticket_number')->toArray();

        if (empty($usedTickets)) {
            // Si no hay tickets ocupados, devolver el primer ticket disponible
            return 1;
        }

        // Encontrar el siguiente número disponible
        $maxTicket = max($usedTickets);

        // Verificar si hay números faltantes en la secuencia
        // Si hay huecos, devolver el primer número disponible
        for ($i = 1; $i <= $maxTicket; $i++) {
            if (!in_array($i, $usedTickets)) {
                return $i;
            }
        }

        // Si no hay huecos, devolver el siguiente número
        return $maxTicket + 1;
    }




    /**
     * Recuperar habitaciones disponibles para una fecha determinada.
     *
     * Esta función calcula el número total de habitaciones disponibles en una fecha específica,
     * considerando si la fecha cae en fin de semana (hay más habitaciones disponibles si es fin de semana).
     * Devuelve una lista de habitaciones que no están ocupadas actualmente.
     *
     * @param string $date Fecha para consultar las habitaciones disponibles.
     * @return array Lista de habitaciones disponibles.
     */
    public function getAvailableRooms(string $date): array
    {
        // Parse the date and determine if it falls on a weekend
        $dateObj = Carbon::parse($date);
        $isWeekend = $dateObj->isWeekend();

        // Set maximum rooms based on whether it's a weekend
        $maxRooms = $isWeekend ? 24 : 14;

        // Retrieve list of used room numbers for the given date
        $usedRooms = Appointment::whereDate('appointment_date', $date)
            ->whereNotNull('room')
            ->whereNull('deleted_at')
            ->pluck('room')
            ->toArray();

        // Calculate available room numbers
        $availableRooms = [];
        for ($i = 1; $i <= $maxRooms; $i++) {
            if (!in_array($i, $usedRooms)) {
                $availableRooms[] = $i;
            }
        }

        return $availableRooms;
    }


    /**
     * Recuperar la cantidad de entradas disponibles para una fecha específica.
     *
     * Esta función calcula la cantidad de entradas disponibles hasta un máximo especificado para una fecha determinada.
     * Excluye las entradas que ya están en uso.
     *
     * @param string $date Fecha para consultar las entradas disponibles.
     * @param int $maxTickets Número máximo de entradas disponibles. El valor predeterminado es 100.
     * @return array Lista de entradas disponibles.
     */
    public function getAvailableTickets(string $date, int $maxTickets = 100): array
    {
        // Fetch all used ticket numbers for the specified date
        $usedTickets = Appointment::whereDate('appointment_date', $date)
            ->whereNotNull('ticket_number')
            ->whereNull('deleted_at')
            ->pluck('ticket_number')
            ->toArray();

        // Determine available ticket numbers by excluding used ones
        $availableTickets = [];
        for ($i = 1; $i <= $maxTickets; $i++) {
            if (!in_array($i, $usedTickets)) {
                $availableTickets[] = $i;
            }
        }

        return $availableTickets;
    }
}
