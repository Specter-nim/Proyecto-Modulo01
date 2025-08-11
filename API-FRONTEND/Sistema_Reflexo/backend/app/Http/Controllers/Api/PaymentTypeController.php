<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PaymentType\PaymentTypeService;
use App\Models\PaymentType;
use App\Http\Requests\PaymentType\StorePaymentTypeRequest;
use App\Http\Requests\PaymentType\UpdatePaymentTypeRequest;
use Illuminate\Http\JsonResponse;

class PaymentTypeController extends Controller
{
    public function __construct(private PaymentTypeService $paymentTypeService)
    {
        $this->paymentTypeService = $paymentTypeService;
        $this->middleware('can:payment-types.store')->only('store');
        $this->middleware('can:payment-types.update')->only('update');
        $this->middleware('can:payment-types.destroy')->only('destroy');
        $this->middleware('can:payment-types.index')->only('index');
        $this->middleware('can:payment-types.show')->only('show');
        
    }

    public function index(): JsonResponse
    {
        return $this->paymentTypeService->getAll();
    }

    public function store(StorePaymentTypeRequest $request): JsonResponse
    {
        return $this->paymentTypeService->storeOrRestore($request->validated());
    }

    public function show(PaymentType $paymentType): JsonResponse
    {
        return response()->json($paymentType);
    }

    public function update(UpdatePaymentTypeRequest $request, PaymentType $paymentType): JsonResponse
    {
        return $this->paymentTypeService->update($paymentType, $request->validated());
    }

    public function destroy(PaymentType $paymentType): JsonResponse
    {
        return $this->paymentTypeService->destroy($paymentType);
    }
}
