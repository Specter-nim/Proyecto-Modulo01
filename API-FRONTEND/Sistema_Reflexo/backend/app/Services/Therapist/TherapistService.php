<?php

namespace App\Services\Therapist;

use App\Models\Therapist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class TherapistService
{
    /**
     * Retorna todos los terapeutas (sin paginar).
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getAll()
    {
        return Therapist::all();
    }

    /**
     * Obtiene una lista paginada de terapeutas.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPaginated(Request $request)
    {
        $perPage = $request->query('per_page', 20);
        return Therapist::orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->appends(['per_page' => $perPage]);
    }

    /**
     * Busca terapeutas según término con paginación.
     *
     * @param array $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function searchTherapists(array $request)
    {
        $perPage = $request['per_page'] ?? 30;
        $searchTerm = $request['search'] ?? '';

        $query = Therapist::query()->orderBy('created_at', 'desc'); // Orden por defecto

        if (!empty($searchTerm)) {
            $query->where(function ($query) use ($searchTerm) {
                $this->searchByTerm($query, $searchTerm);
            });
        }

        return $query->paginate($perPage)->appends([
            'per_page' => $perPage,
            'search' => $searchTerm,
        ]);
    }

    /**
     * Crea o restaura terapeuta.
     *
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeOrRestore(array $data)
    {
        try {
            $therapist = Therapist::withTrashed()->firstOrNew([
                'document_number' => $data['document_number'],
            ]);

            if ($therapist->exists) {
                if ($therapist->trashed()) {
                    $therapist->restore();
                    return response()->json([
                        'message' => 'El terapeuta fue restaurado',
                        'data' => $therapist->fresh(),
                    ], 200);
                }
                return response()->json(['message' => 'El terapeuta ya existe'], 409);
            }

            $therapist->fill($data)->save();

            return response()->json($therapist, 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al procesar la solicitud',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Actualiza terapeuta con datos modificados.
     *
     * @param Therapist $therapist
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Therapist $therapist, array $data)
    {
        $filteredData = array_filter(
            $data,
            fn($key, $value) => (string) $therapist->$key !== (string) $value,
            ARRAY_FILTER_USE_BOTH
        );

        if (!empty($filteredData)) {
            $therapist->update($filteredData);
        }

        return response()->json($therapist);
    }

    /**
     * Eliminación suave de terapeuta.
     *
     * @param Therapist $therapist
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Therapist $therapist)
    {
        $therapist->delete();
        return response()->json(['message' => 'Terapeuta eliminado correctamente'], 200);
    }

    /**
     * Búsqueda avanzada por término en varios campos.
     *
     * @param Builder $query
     * @param string $searchTerm
     * @return Builder
     */
    private function searchByTerm(Builder $query, string $searchTerm): Builder
    {
        return $query->where(function ($q) use ($searchTerm) {
            $q->where('document_number', '=', $searchTerm)
                ->orWhere('document_number', 'LIKE', "{$searchTerm}%")
                ->orWhere('code', '=', $searchTerm)
                ->orWhere('name', 'LIKE', "{$searchTerm}%")
                ->orWhere('name', 'LIKE', "% {$searchTerm}%")
                ->orWhere('paternal_lastname', 'LIKE', "{$searchTerm}%")
                ->orWhere('paternal_lastname', 'LIKE', "% {$searchTerm}%")
                ->orWhere('maternal_lastname', 'LIKE', "{$searchTerm}%")
                ->orWhere('maternal_lastname', 'LIKE', "% {$searchTerm}%")
                ->orWhereRaw("CONCAT(paternal_lastname, ' ', name) LIKE ?", ["{$searchTerm}%"])
                ->orWhereRaw("CONCAT(paternal_lastname, ' ', maternal_lastname, ' ', name) LIKE ?", ["{$searchTerm}%"])
                ->orWhereRaw("CONCAT(name, ' ', paternal_lastname, ' ', maternal_lastname) LIKE ?", ["{$searchTerm}%"])
                ->orWhereRaw("CONCAT(COALESCE(paternal_lastname, ''), ' ', COALESCE(SUBSTRING_INDEX(name, ' ', 1), ''), ' ', COALESCE(SUBSTRING_INDEX(name, ' ', -1), '')) LIKE ?", ["{$searchTerm}%"])
                ->orWhereRaw("CONCAT(COALESCE(maternal_lastname, ''), ' ', COALESCE(SUBSTRING_INDEX(name, ' ', 1), ''), ' ', COALESCE(SUBSTRING_INDEX(name, ' ', -1), '')) LIKE ?", ["{$searchTerm}%"])
                ->orWhereRaw("CONCAT(COALESCE(SUBSTRING_INDEX(name, ' ', 1), ''), ' ', COALESCE(paternal_lastname, ''), ' ', COALESCE(maternal_lastname, '')) LIKE ?", ["{$searchTerm}%"])
                ->orWhereRaw("CONCAT(COALESCE(SUBSTRING_INDEX(name, ' ', -1), ''), ' ', COALESCE(paternal_lastname, ''), ' ', COALESCE(maternal_lastname, '')) LIKE ?", ["{$searchTerm}%"]);
        });
    }
}