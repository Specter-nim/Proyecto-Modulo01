<?php

namespace App\Services\PaymentType;

use App\Models\PaymentType;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class PaymentTypeService
{
    public function getAll(): JsonResponse
    {
        $paymentTypes = PaymentType::withTrashed()->get();

        return $paymentTypes->isEmpty()
            ? response()->json(['message' => 'Aún no se ha creado ningún tipo de pago'], Response::HTTP_OK)
            : response()->json($paymentTypes, Response::HTTP_OK);
    }

    public function storeOrRestore(array $data): JsonResponse
    {
        $paymentType = PaymentType::withTrashed()->firstOrNew(['name' => $data['name']]);

        if ($paymentType->exists) {
            if ($paymentType->trashed()) {
                $paymentType->restore();
                return response()->json([
                    'message' => 'El tipo de Paymento fue restaurado',
                    'data' => $paymentType->fresh()
                ], Response::HTTP_OK);
            }

            return response()->json(['message' => 'El tipo de pago ya existe'], Response::HTTP_CONFLICT);
        }

        $paymentType->fill($data)->save();
        return response()->json($paymentType, Response::HTTP_CREATED);
    }

    public function update(PaymentType $paymentType, array $data): JsonResponse
    {
        $existingPaymentType = PaymentType::withTrashed()
            ->where('name', $data['name'])
            ->where('id', '!=', $paymentType->id)
            ->first();

        if ($existingPaymentType) {
            return response()->json(['message' => 'El tipo de pago ya existe'], Response::HTTP_CONFLICT);
        }

        $paymentType->update($data);
        return response()->json($paymentType);
    }

    public function destroy(PaymentType $paymentType): JsonResponse
    {
        $paymentType->delete();
        return response()->json(['message' => 'Tipo de pago eliminado correctamente'], Response::HTTP_OK);
    }
}
