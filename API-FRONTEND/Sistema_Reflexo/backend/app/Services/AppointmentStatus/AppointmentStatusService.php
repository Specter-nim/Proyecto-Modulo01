<?php

namespace App\Services\AppointmentStatus;

use App\Models\AppointmentStatus;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AppointmentStatusService
{
    public function getAll(): JsonResponse
    {
        $appointmentStatuses = AppointmentStatus::all();

        return $appointmentStatuses->isEmpty()
            ? response()->json(['message' => 'Aún no se ha creado ningún estado de cita'], Response::HTTP_OK)
            : response()->json($appointmentStatuses);
    }

    public function storeOrRestore(array $data): JsonResponse
    {
        $appointmentStatus = AppointmentStatus::withTrashed()->firstOrNew(['name' => $data['name']]);

        if ($appointmentStatus->exists) {
            if ($appointmentStatus->trashed()) {
                $appointmentStatus->restore();
                return response()->json([
                    'message' => 'El estado de cita fue restaurado',
                    'data' => $appointmentStatus->fresh()
                ], Response::HTTP_OK);
            }

            return response()->json(['message' => 'El estado de cita ya existe'], Response::HTTP_CONFLICT);
        }

        $appointmentStatus->fill($data)->save();
        return response()->json($appointmentStatus, Response::HTTP_CREATED);
    }

    public function update(AppointmentStatus $appointmentStatus, array $data): JsonResponse
    {
        $existingAppointmentStatus = AppointmentStatus::withTrashed()
            ->where('name', $data['name'])
            ->where('id', '!=', $appointmentStatus->id)
            ->first();

        if ($existingAppointmentStatus) {
            return response()->json(['message' => 'El estado de cita de documento ya existe'], Response::HTTP_CONFLICT);
        }

        $appointmentStatus->update($data);
        return response()->json($appointmentStatus);
    }

    public function destroy(AppointmentStatus $appointmentStatus): JsonResponse
    {
        $appointmentStatus->delete();
        return response()->json(['message' => 'Estado de cita eliminado correctamente'], Response::HTTP_OK);
    }
}
