<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Appointment\AppointmentsPaginatedByDateRequest;
use App\Http\Requests\Appointment\CompletedAppointmentsCalendarByDateRequest;
use App\Http\Requests\Appointment\CompletedAppointmentsPaginatedByDateRequest;
use App\Http\Requests\Appointment\PendingAppointmentsCalendarByDateRequest;
use App\Http\Requests\Appointment\SearchAppointmentsRequest;
use App\Http\Requests\Appointment\SearchCompletedAppointmentsRequest;
use App\Http\Requests\Appointment\StoreAppointmentRequest;
use App\Http\Requests\Appointment\UpdateAppointmentRequest;
use App\Models\Appointment;
use App\Services\Appointment\AppointmentService;
use Illuminate\Http\JsonResponse;

class AppointmentController extends Controller
{
    public function __construct(private AppointmentService $appointmentService) {
        $this->appointmentService = $appointmentService;
        
        $this->middleware('can:appointments.store')->only('store');
        $this->middleware('can:appointments.update')->only('update');
        $this->middleware('can:appointments.destroy')->only('destroy');
        $this->middleware('can:appointments.index')->only('index');
        $this->middleware('can:appointments.show')->only('show');

        // Protegiendo nuevos métodos
        $this->middleware('can:appointments.search')->only('searchAppointments');
        $this->middleware('can:appointments.search_completed')->only('searchCompletedAppointments');
        $this->middleware('can:appointments.paginated_by_date')->only('getPaginatedAppointmentsByDate');
        $this->middleware('can:appointments.completed_paginated_by_date')->only('getCompletedAppointmentsPaginatedByDate');
        $this->middleware('can:appointments.pending_calendar_by_date')->only('getPendingAppointmentsForCalendarByDate');
        $this->middleware('can:appointments.completed_calendar_by_date')->only('getCompletedAppointmentsForCalendarByDate');
    }

    public function searchAppointments(SearchAppointmentsRequest $request): JsonResponse
    {
        return $this->appointmentService->searchAppointments($request->validated());
    }

    public function searchCompletedAppointments(SearchCompletedAppointmentsRequest $request): JsonResponse
    {
        return $this->appointmentService->searchCompletedAppointments($request->validated());
    }

    public function getPaginatedAppointmentsByDate(AppointmentsPaginatedByDateRequest $request): JsonResponse 
    {
        return $this->appointmentService->getPaginatedAppointmentsByDate($request->validated());        
    }

    public function getCompletedAppointmentsPaginatedByDate(CompletedAppointmentsPaginatedByDateRequest $request): JsonResponse 
    {
        return $this->appointmentService->getCompletedAppointmentsPaginatedByDate($request->validated());    
    }

    public function getPendingAppointmentsForCalendarByDate(): JsonResponse 
    {
        return $this->appointmentService->getPendingAppointmentsForCalendarByDate();    
    }

    public function getCompletedAppointmentsForCalendarByDate(CompletedAppointmentsCalendarByDateRequest $request): JsonResponse 
    {
        return $this->appointmentService->getCompletedAppointmentsForCalendarByDate($request->validated());    
    }
    
    public function store(StoreAppointmentRequest $request): JsonResponse
    {
        return $this->appointmentService->storeOrRestore($request->validated());
    }

    public function show(Appointment $appointment): JsonResponse
    {
        return response()->json($appointment);
    }

    public function update(UpdateAppointmentRequest $request, Appointment $appointment): JsonResponse
    {
        return $this->appointmentService->update($appointment, $request->validated());
    }

    public function destroy(Appointment $appointment): JsonResponse
    {
        return $this->appointmentService->destroy($appointment);
    }

}
