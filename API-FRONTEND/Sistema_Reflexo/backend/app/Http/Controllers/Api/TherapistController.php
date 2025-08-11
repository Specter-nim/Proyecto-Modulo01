<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Therapist\SearchTherapistsRequest;
use App\Services\Therapist\TherapistService;
use App\Models\Therapist;
use App\Http\Requests\Therapist\StoreTherapistRequest;
use App\Http\Requests\Therapist\UpdateTherapistRequest;
use App\Http\Resources\Therapist\TherapistResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Resources\Therapist\TherapistCollection;

class TherapistController extends Controller
{
    public function __construct(private TherapistService $therapistService)
    {
        $this->therapistService = $therapistService;
        $this->middleware('can:therapists.store')->only('store');
        $this->middleware('can:therapists.update')->only('update');
        $this->middleware('can:therapists.destroy')->only('destroy');
        $this->middleware('can:therapists.index')->only('index');
        $this->middleware('can:therapists.show')->only('show');

        // Protegiendo métodos adicionales
        $this->middleware('can:therapists.search')->only('searchTherapists');
    }

    public function index(Request $request): TherapistCollection
    {
        $therapists = $this->therapistService->getPaginated($request);
        return new TherapistCollection($therapists);
    }

    public function searchTherapists(SearchTherapistsRequest $request): TherapistCollection
    {
        $therapists = $this->therapistService->searchTherapists($request->validated());
        return new TherapistCollection($therapists);
    }

    public function show(Therapist $therapist): JsonResponse
    {
        return response()->json(new TherapistResource($therapist));
    }

    public function store(StoreTherapistRequest $request): JsonResponse
    {
        return $this->therapistService->storeOrRestore($request->validated());
    }

    public function update(UpdateTherapistRequest $request, Therapist $therapist): JsonResponse
    {
        return $this->therapistService->update($therapist, $request->validated());
    }

    public function destroy(Therapist $therapist): JsonResponse
    {
        return $this->therapistService->destroy($therapist);
    }
}