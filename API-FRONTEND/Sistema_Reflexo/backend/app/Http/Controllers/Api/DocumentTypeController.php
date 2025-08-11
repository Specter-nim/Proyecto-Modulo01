<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DocumentType\DocumentTypeService;
use App\Models\DocumentType;
use App\Http\Requests\DocumentType\StoreDocumentTypeRequest;
use App\Http\Requests\DocumentType\UpdateDocumentTypeRequest;
use Illuminate\Http\JsonResponse;

class DocumentTypeController extends Controller
{
    
    public function __construct(private DocumentTypeService $documentTypeService)
    {
        $this->documentTypeService = $documentTypeService;
        $this->middleware('can:document-types.store')->only('store');
        $this->middleware('can:document-types.update')->only('update');
        $this->middleware('can:document-types.destroy')->only('destroy');
        $this->middleware('can:document-types.index')->only('index');
        $this->middleware('can:document-types.show')->only('show');
    }

    public function index(): JsonResponse
    {
        return $this->documentTypeService->getAll();
    }

    public function store(StoreDocumentTypeRequest $request): JsonResponse
    {
        return $this->documentTypeService->storeOrRestore($request->validated());
    }

    public function show(DocumentType $documentType): JsonResponse
    {
        return response()->json($documentType);
    }

    public function update(UpdateDocumentTypeRequest $request, DocumentType $documentType): JsonResponse
    {
        return $this->documentTypeService->update($documentType, $request->validated());
    }

    public function destroy(DocumentType $documentType): JsonResponse
    {
        return $this->documentTypeService->destroy($documentType);
    }
}
