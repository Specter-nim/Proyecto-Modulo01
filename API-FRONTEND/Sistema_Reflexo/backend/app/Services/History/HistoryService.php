<?php

namespace App\Services\History;

use App\Models\History;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HistoryService
{
    public function getAll(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 20);
        $page = $request->input('page', 1);

        $histories = History::paginate($perPage, ['*'], 'page', $page);

        return $histories->isEmpty()
            ? response()->json(
                [
                    'message' => 'No se han registrado historias médicas aún.'
                ],
                Response::HTTP_OK
            )
            : response()->json($histories, Response::HTTP_OK);
    }


    public function storeOrRestore(array $data): JsonResponse
    {
        try {
            // Verificar si el paciente ya tiene un historial, incluyendo los eliminados
            $history = History::withTrashed()->firstOrNew([
                'patient_id' => $data['patient_id'],
            ]);

            if ($history->exists) {
                // Si ya existe y está en estado "soft deleted", restauramos el registro
                if ($history->trashed()) {
                    $history->restore();
                    return response()->json([
                        'message' => 'El historial fue restaurado exitosamente.',
                        'data' => $history->fresh(),
                    ], Response::HTTP_OK);
                }

                // Si el historial ya existe y no está eliminado, informamos al usuario
                return response()->json([
                    'message' => 'El historial médico de este paciente ya está registrado.',
                ], Response::HTTP_CONFLICT);
            }

            // Si no existe, creamos un nuevo registro
            $history->fill($data)->save();

            return response()->json([
                'message' => 'El historial médico fue creado exitosamente.',
                'data' => $history,
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            // Capturamos cualquier error inesperado
            return response()->json([
                'message' => 'Ocurrió un error al procesar la solicitud.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(History $history, array $data): JsonResponse
    {
        try {
            // Filtramos los datos que realmente han cambiado
            $filteredData = array_filter(
                $data,
                fn($value, $key) => (string) $history->$key !== (string) $value,
                ARRAY_FILTER_USE_BOTH
            );

            if (!empty($filteredData)) {
                // Si hay datos modificados, actualizamos el historial
                $history->update($filteredData);
                return response()->json([
                    'message' => 'El historial médico fue actualizado exitosamente.',
                    'data' => $history,
                ], Response::HTTP_OK);
            }

            return response()->json([
                'message' => 'No se realizaron cambios en el historial médico.',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ocurrió un error al actualizar el historial médico.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(History $history): JsonResponse
    {
        try {
            $history->delete();
            return response()->json([
                'message' => 'El historial médico fue eliminado exitosamente.',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ocurrió un error al eliminar el historial médico.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

public function getByPatient(Patient $patient): JsonResponse
{
    try {
        // Obtener el historial del paciente (incluyendo información del paciente)
        $history = History::with('patient')
            ->where('patient_id', $patient->id)
            ->first();

        if (!$history) {
            return response()->json([
                'message' => 'Este paciente no tiene historial médico registrado.',
                'data' => null
            ], Response::HTTP_OK);
        }

        return response()->json([
            'message' => 'Historial médico obtenido exitosamente.',
            'data' => $history
        ], Response::HTTP_OK);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Ocurrió un error al obtener el historial médico.',
            'error' => $e->getMessage(),
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}

}
