<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UploadImageRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    public function __construct()
    {
        // Usar los permisos correctos que tienes definidos en tu PermissionSeeder
        $this->middleware('can:users.photo.upload')->only(['uploadUserPhoto']);
        $this->middleware('can:users.photo.show')->only(['showUserPhoto']);
        $this->middleware('can:users.photo.delete')->only(['deleteUserPhoto']);
    }

    /**
     * Subir foto de perfil del usuario
     */
    public function uploadUserPhoto(UploadImageRequest $request, User $user): JsonResponse
    {
        try {
            // Eliminar imagen anterior si existe
            if ($user->photo_url && Storage::disk('public')->exists($user->photo_url)) {
                Storage::disk('public')->delete($user->photo_url);
            }

            // Generar nombre alfanumérico aleatorio de máximo 15 caracteres
            $fileName = $this->generateRandomFileName($request->file('photo'));
            
            // Subir nueva imagen con el nombre personalizado
            $path = $request->file('photo')->storeAs('images/users', $fileName, 'public');
            
            // Actualizar usuario
            $user->update(['photo_url' => $path]);

            return response()->json([
                'message' => 'Foto actualizada correctamente',
                'photo_url' => $path,
                'full_url' => Storage::url($path),
                'file_name' => $fileName
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al subir la imagen',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Mostrar foto del usuario
     */
    public function showUserPhoto(User $user)
    {
        if (!$user->photo_url || !Storage::disk('public')->exists($user->photo_url)) {
            abort(404, 'Imagen no encontrada');
        }

        $path = storage_path('app/public/' . $user->photo_url);
        $mimeType = mime_content_type($path);

        return response()->file($path, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"'
        ]);
    }

    /**
     * Eliminar foto del usuario
     */
    public function deleteUserPhoto(User $user): JsonResponse
    {
        try {
            if ($user->photo_url && Storage::disk('public')->exists($user->photo_url)) {
                Storage::disk('public')->delete($user->photo_url);
                
                $user->update(['photo_url' => null]);

                return response()->json([
                    'message' => 'Foto eliminada correctamente'
                ], Response::HTTP_OK);
            }

            return response()->json([
                'message' => 'No hay foto para eliminar'
            ], Response::HTTP_NOT_FOUND);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar la imagen',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Generar nombre de archivo alfanumérico aleatorio
     */
    private function generateRandomFileName($file): string
    {
        // Obtener la extensión original del archivo
        $extension = $file->getClientOriginalExtension();
        
        // Generar nombre aleatorio alfanumérico de 15 caracteres
        $randomName = Str::random(15);
        
        // Asegurar que solo contenga caracteres alfanuméricos
        $randomName = preg_replace('/[^a-zA-Z0-9]/', '', $randomName);
        
        // Si queda muy corto, completar con más caracteres
        while (strlen($randomName) < 15) {
            $randomName .= substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 15 - strlen($randomName));
        }
        
        // Asegurar exactamente 15 caracteres
        $randomName = substr($randomName, 0, 15);
        
        return $randomName . '.' . $extension;
    }
}