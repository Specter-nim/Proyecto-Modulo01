<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UploadImageRequest;
use App\Models\CompanyData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class CompanyController extends Controller
{
    public function __construct()
    {
        // Agregar middlewares de permisos si los necesitas
        $this->middleware('can:company.create')->only(['store']);
        $this->middleware('can:company.update')->only(['update']);
        $this->middleware('can:company.logo.upload')->only(['uploadLogo']);
        $this->middleware('can:company.view')->only(['show']);
        $this->middleware('can:company.logo.show')->only(['showLogo']);
        $this->middleware('can:company.logo.delete')->only(['deleteLogo']);
    }

    /**
     * Obtener datos de la empresa
     */
    public function show(): JsonResponse
    {
        $company = CompanyData::first();

        if (!$company) {
            return response()->json([
                'message' => 'No se encontraron datos de la empresa'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'data' => [
                'id' => $company->id,
                'company_name' => $company->company_name,
                'company_logo' => $company->company_logo,
                'logo_url' => $company->logo_url,
                'has_logo' => $company->hasLogo(),
                'created_at' => $company->created_at,
                'updated_at' => $company->updated_at,
            ]
        ], Response::HTTP_OK);
    }

    /**
     * Crear o actualizar datos de la empresa
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            // Buscar si ya existe una empresa (solo habrá una)
            $company = CompanyData::first();

            if ($company) {
                // Actualizar empresa existente
                $company->update(['company_name' => $request->company_name]);

                // Si hay nuevo logo, procesarlo
                if ($request->hasFile('logo')) {
                    $this->processLogo($company, $request->file('logo'));
                }

                $message = 'Datos de la empresa actualizados correctamente';
            } else {
                // Crear nueva empresa primero SIN el logo
                $company = CompanyData::create([
                    'company_name' => $request->company_name
                ]);

                // Después procesar el logo si existe
                if ($request->hasFile('logo')) {
                    $this->processLogo($company, $request->file('logo'));
                }

                $message = 'Datos de la empresa creados correctamente';
            }

            // Refrescar el modelo para obtener los datos actualizados
            $company->refresh();

            return response()->json([
                'message' => $message,
                'data' => [
                    'id' => $company->id,
                    'company_name' => $company->company_name,
                    'company_logo' => $company->company_logo,
                    'logo_url' => $company->logo_url,
                ]
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al guardar los datos de la empresa',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Subir logo de la empresa
     */
    public function uploadLogo(UploadImageRequest $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $company = CompanyData::first();

            if (!$company) {
                return response()->json([
                    'message' => 'Primero debe crear los datos de la empresa'
                ], Response::HTTP_NOT_FOUND);
            }

            $this->processLogo($company, $request->file('logo'));

            return response()->json([
                'message' => 'Logo actualizado correctamente',
                'company_logo' => $company->company_logo,
                'logo_url' => $company->logo_url,
                'file_name' => basename($company->company_logo)
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al subir el logo',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Mostrar logo de la empresa
     */
    public function showLogo()
    {
        $company = CompanyData::first();

        if (!$company || !$company->hasLogo()) {
            abort(404, 'Logo no encontrado');
        }

        $path = storage_path('app/public/' . $company->company_logo);
        $mimeType = mime_content_type($path);

        return response()->file($path, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"'
        ]);
    }

    /**
     * Eliminar logo de la empresa
     */
    public function deleteLogo(): JsonResponse
    {
        try {
            $company = CompanyData::first();

            if (!$company) {
                return response()->json([
                    'message' => 'No se encontraron datos de la empresa'
                ], Response::HTTP_NOT_FOUND);
            }

            // Eliminar todas las imágenes de la carpeta company
            $this->clearCompanyFolder();

            // Actualizar el registro
            $company->update(['company_logo' => null]);

            return response()->json([
                'message' => 'Logo eliminado correctamente'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar el logo',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Procesar y guardar el logo de la empresa
     */
    private function processLogo(CompanyData $company, $file): void
    {
        // Limpiar toda la carpeta company antes de subir el nuevo logo
        $this->clearCompanyFolder();

        // Generar nombre del archivo usando el nombre de la empresa
        $fileName = $this->generateCompanyLogoFileName($company->company_name, $file);

        // Subir nueva imagen con el nombre personalizado en la carpeta company
        $path = $file->storeAs('company', $fileName, 'public');

        // Actualizar empresa con la nueva ruta del logo
        $company->update(['company_logo' => $path]);
    }

    /**
     * Limpiar toda la carpeta company
     */
    private function clearCompanyFolder(): void
    {
        // Obtener todos los archivos de la carpeta company
        $files = Storage::disk('public')->files('company');

        // Eliminar todos los archivos
        if (!empty($files)) {
            Storage::disk('public')->delete($files);
        }
    }

    /**
     * Generar nombre de archivo usando el nombre de la empresa
     */
    private function generateCompanyLogoFileName(string $companyName, $file): string
    {
        // Obtener la extensión original del archivo
        $extension = $file->getClientOriginalExtension();

        // Limpiar el nombre de la empresa para usar como nombre de archivo
        $cleanCompanyName = $this->sanitizeFileName($companyName);

        // Crear el nombre del archivo: {nombre_empresa}_logo.{extension}
        return $cleanCompanyName . '_logo.' . $extension;
    }

    /**
     * Limpiar string para usar como nombre de archivo
     */
    private function sanitizeFileName(string $string): string
    {
        // Convertir a minúsculas
        $string = strtolower($string);

        // Reemplazar espacios y caracteres especiales con guiones bajos
        $string = preg_replace('/[^a-z0-9]+/', '_', $string);

        // Remover guiones bajos al inicio y final
        $string = trim($string, '_');

        // Limitar longitud si es muy largo
        if (strlen($string) > 50) {
            $string = substr($string, 0, 50);
        }

        // Si queda vacío, usar un nombre por defecto
        if (empty($string)) {
            $string = 'company';
        }

        return $string;
    }
}
