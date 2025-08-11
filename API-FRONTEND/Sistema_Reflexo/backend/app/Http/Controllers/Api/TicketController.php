<?php

namespace App\Http\Controllers\Api;

use App\Models\Appointment;
use App\Http\Controllers\Controller;
use App\Services\Ticket\TicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class TicketController extends Controller
{
    public function __construct(private TicketService $ticketService)
    {
        $this->middleware('auth:sanctum');
        $this->middleware('can:tickets.index')->only('index');
        $this->middleware('can:tickets.available')->only('availableTickets');
        $this->middleware('can:tickets.nextRoom')->only('getNextRoomNumber');
        $this->middleware('can:tickets.nextTicket')->only('getNextTicketNumber');
        $this->middleware('can:tickets.stats')->only('getResourceStats');
    }

  
    /**
     * Retrieve available rooms and tickets for a specific date.
     *
     * This method validates the request for a date and uses the TicketService
     * to fetch the list of available rooms and tickets for that date.
     *
     * @param Request $request The HTTP request object containing the date parameter.
     * @return JsonResponse A JSON response containing lists of available rooms and tickets.
     */

    public function availableTickets(Request $request): JsonResponse
    {
        $request->validate(['date' => 'required|date']);

        $availableRooms = $this->ticketService->getAvailableRooms($request->date);
        $availableTickets = $this->ticketService->getAvailableTickets($request->date);

        return response()->json([
            'available_rooms' => $availableRooms,
            'available_tickets' => $availableTickets
        ]);
    }

    /**
     * Get the next available room number for a given date
     */
    public function getNextRoomNumber(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date',
            'appointment_id' => 'sometimes|integer'
        ]);

        $excludeAppointmentId = $request->appointment_id ?? null;
        $roomNumber = $this->ticketService->generateNextRoomNumber($request->date, $excludeAppointmentId);

        return response()->json([
            'room_number' => $roomNumber
        ], Response::HTTP_OK);
    }

    /**
     * Get the next available ticket number for a given date
     */
    public function getNextTicketNumber(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date',
            'appointment_id' => 'sometimes|integer'
        ]);

        $excludeAppointmentId = $request->appointment_id ?? null;
        $ticketNumber = $this->ticketService->generateNextTicketNumber($request->date, $excludeAppointmentId);

        return response()->json([
            'ticket_number' => $ticketNumber
        ], Response::HTTP_OK);
    }
    
    /**
     * Obtener estadísticas de uso de recursos para una fecha
     */
    public function getResourceStats(Request $request): JsonResponse
    {
        $request->validate(['date' => 'required|date']);
        
        $date = $request->date;
        $isWeekend = Carbon::parse($date)->isWeekend();
        $maxRooms = $isWeekend ? 24 : 14;
        
        $appointmentsCount = Appointment::whereDate('appointment_date', $date)
            ->whereNull('deleted_at')
            ->count();
            
        $roomsUsed = Appointment::whereDate('appointment_date', $date)
            ->whereNotNull('room')
            ->whereNull('deleted_at')
            ->count();
            
        $ticketsUsed = Appointment::whereDate('appointment_date', $date)
            ->whereNotNull('ticket_number')
            ->whereNull('deleted_at')
            ->count();
        
        return response()->json([
            'date' => $date,
            'is_weekend' => $isWeekend,
            'max_rooms' => $maxRooms,
            'total_appointments' => $appointmentsCount,
            'rooms_used' => $roomsUsed,
            'rooms_available' => $maxRooms - $roomsUsed,
            'tickets_used' => $ticketsUsed,
            'room_usage_percentage' => $maxRooms > 0 ? round(($roomsUsed / $maxRooms) * 100, 2) : 0
        ]);
    }
}