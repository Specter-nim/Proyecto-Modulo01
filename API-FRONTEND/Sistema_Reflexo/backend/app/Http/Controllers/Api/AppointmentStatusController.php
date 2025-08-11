<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AppointmentStatus\StoreAppointmentStatusRequest;
use App\Http\Requests\AppointmentStatus\UpdateAppointmentStatusRequest;
use Illuminate\Http\JsonResponse;
use App\Models\AppointmentStatus;
use App\Services\AppointmentStatus\AppointmentStatusService;

class AppointmentStatusController extends Controller
{
    public function __construct(private AppointmentStatusService $appointmentStatusService)
    {
        $this->appointmentStatusService = $appointmentStatusService;
        $this->middleware('can:appointment-statuses.store')->only('store');
        $this->middleware('can:appointment-statuses.update')->only('update');
        $this->middleware('can:appointment-statuses.destroy')->only('destroy');
        $this->middleware('can:appointment-statuses.index')->only('index');
        $this->middleware('can:appointment-statuses.show')->only('show');
    }

    public function index(): JsonResponse
    {
        return $this->appointmentStatusService->getAll();
    }

    public function store(StoreAppointmentStatusRequest $request): JsonResponse
    {
        return $this->appointmentStatusService->storeOrRestore($request->validated());
    }

    public function show(AppointmentStatus $appointmentStatus): JsonResponse
    {
        return response()->json($appointmentStatus);
    }

    public function update(UpdateAppointmentStatusRequest $request, AppointmentStatus $appointmentStatus): JsonResponse
    {
        return $this->appointmentStatusService->update($appointmentStatus, $request->validated());
    }

    public function destroy(AppointmentStatus $appointmentStatus): JsonResponse
    {
        return $this->appointmentStatusService->destroy($appointmentStatus);
    }
}
