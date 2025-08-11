<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\History\HistoryService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\History\StoreHistoryRequest;
use App\Http\Requests\History\UpdateHistoryRequest;
use App\Models\History;
use App\Models\Patient;
use Illuminate\Http\Request;

class HistoryController extends Controller
{

    public function __construct(private HistoryService $historyService)
    {
        $this->historyService = $historyService;
        $this->middleware('can:histories.store')->only('store');
        $this->middleware('can:histories.update')->only('update');
        $this->middleware('can:histories.destroy')->only('destroy');
        $this->middleware('can:histories.index')->only('index');
        $this->middleware('can:histories.show')->only('show');
    }

    public function index(Request $request): JsonResponse
    {
        return $this->historyService->getAll($request);
    }

    public function store(StoreHistoryRequest $request): JsonResponse
    {
        return $this->historyService->storeOrRestore($request->validated());
    }

    public function show(History $history): JsonResponse
    {
        return response()->json($history);
    }

    public function update(UpdateHistoryRequest $request, History $history): JsonResponse
    {
        return $this->historyService->update($history, $request->validated());
    }

    public function destroy(History $history): JsonResponse
    {
        return $this->historyService->destroy($history);
    }

    public function getByPatient(Patient $patient): JsonResponse
    {
        return $this->historyService->getByPatient($patient);
    }
}
