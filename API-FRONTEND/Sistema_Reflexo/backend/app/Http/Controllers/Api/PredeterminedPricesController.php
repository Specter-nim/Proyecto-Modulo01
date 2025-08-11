<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\PredeterminedPrice\StorePredeterminedPriceRequest;
use App\Http\Requests\PredeterminedPrice\UpdatePredeterminedPriceRequest;
use App\Models\PredeterminedPrice;
use App\Services\PredeterminedPrice\PredeterminedPriceService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PredeterminedPricesController extends Controller
{
    public function __construct(private PredeterminedPriceService $predeterminedPriceService)
    {
        $this->predeterminedPriceService = $predeterminedPriceService;
        $this->middleware('can:predetermined-prices.store')->only('store');
        $this->middleware('can:predetermined-prices.update')->only('update');
        $this->middleware('can:predetermined-prices.destroy')->only('destroy');
        $this->middleware('can:predetermined-prices.index')->only('index');
        $this->middleware('can:predetermined-prices.show')->only('show');
    }

    public function index(): JsonResponse
    {
        return $this->predeterminedPriceService->getAll();
    }

    public function store(StorePredeterminedPriceRequest $request): JsonResponse
    {
        return $this->predeterminedPriceService->storeOrRestore($request->validated());
    }

    public function show(PredeterminedPrice $predeterminedPrice): JsonResponse
    {
        return response()->json($predeterminedPrice);
    }

    public function update(UpdatePredeterminedPriceRequest $request, PredeterminedPrice $predeterminedPrice): JsonResponse
    {
        return $this->predeterminedPriceService->update($predeterminedPrice, $request->validated());
    }

    public function destroy(PredeterminedPrice $predeterminedPrice): JsonResponse
    {
        return $this->predeterminedPriceService->destroy($predeterminedPrice);
    }
}