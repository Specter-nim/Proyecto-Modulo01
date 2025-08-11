<?php

namespace App\Services\DocumentType;

use App\Models\DocumentType;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;


class DocumentTypeService
{
    public function getAll(): JsonResponse
    {
        $documents = DocumentType::all();

        return $documents->isEmpty()
            ? response()->json(['message' => 'Aún no se ha creado ningún tipo de documento'], Response::HTTP_OK)
            : response()->json($documents);
    }

    public function storeOrRestore(array $data): JsonResponse
    {
        $documentType = DocumentType::withTrashed()->firstOrNew(['name' => $data['name']]);

        if ($documentType->exists) {
            if ($documentType->trashed()) {
                $documentType->restore();
                return response()->json([
                    'message' => 'El tipo de documento fue restaurado',
                    'data' => $documentType->fresh()
                ], Response::HTTP_OK);
            }

            return response()->json(['message' => 'El tipo de documento ya existe'], Response::HTTP_CONFLICT);
        }

        $documentType->fill($data)->save();
        return response()->json($documentType, Response::HTTP_CREATED);
    }




    public function update(DocumentType $documentType, array $data): JsonResponse
    {
        $existingDocument = DocumentType::withTrashed()
            ->where('name', $data['name'])
            ->where('id', '!=', $documentType->id)
            ->first();

        if ($existingDocument) {
            return response()->json(['message' => 'El tipo de documento ya existe'], Response::HTTP_CONFLICT);
        }

        $documentType->update($data);
        return response()->json($documentType);
    }

    public function destroy(DocumentType $documentType): JsonResponse
    {
        $documentType->delete();
        return response()->json(['message' => 'Tipo de documento eliminado correctamente'], Response::HTTP_OK);
    }
}
