<?php

namespace App\Services\PredeterminedPrice;

use App\Models\PredeterminedPrice;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PredeterminedPriceService
{
    public function getAll(): JsonResponse
    {
        $predeterminedPrices = PredeterminedPrice::withTrashed()->get();

        return $predeterminedPrices->isEmpty()
            ? response()->json(['message' => 'Aún no se ha creado ningún precio predeterminado'], Response::HTTP_OK)
            : response()->json($predeterminedPrices);
    }

    public function storeOrRestore(array $data): JsonResponse
    {
        $predeterminedPrice = PredeterminedPrice::withTrashed()->firstOrNew(['name' => $data['name']]);

        if ($predeterminedPrice->exists) {
            if ($predeterminedPrice->trashed()) {
                $predeterminedPrice->restore();
                return response()->json([
                    'message' => 'El precio predeterminado fue restaurado',
                    'data' => $predeterminedPrice->fresh()
                ], Response::HTTP_OK);
            }

            return response()->json(['message' => 'El precio predeterminado ya existe'], Response::HTTP_CONFLICT);
        }

        $predeterminedPrice->fill($data)->save();
        return response()->json($predeterminedPrice, Response::HTTP_CREATED);
    }

    public function update(PredeterminedPrice $predeterminedPrice, array $data): JsonResponse
    {
        $existingPredeterminedPrice = PredeterminedPrice::withTrashed()
            ->where('name', $data['name'])
            ->where('id', '!=', $predeterminedPrice->id)
            ->first();

        if ($existingPredeterminedPrice) {
            return response()->json(['message' => 'El precio predeterminado ya existe'], Response::HTTP_CONFLICT);
        }

        $predeterminedPrice->update($data);
        return response()->json($predeterminedPrice);
    }

    public function destroy(PredeterminedPrice $predeterminedPrice): JsonResponse
    {
        $predeterminedPrice->delete();
        return response()->json(['message' => 'Precio predeterminado eliminado correctamente'], Response::HTTP_OK);
    }
}
