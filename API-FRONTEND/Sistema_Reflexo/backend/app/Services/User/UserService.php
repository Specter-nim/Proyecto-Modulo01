<?php

namespace App\Services\User;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\User\UserResource;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserService
{
    /**
     * Obtener todos los usuarios con sus relaciones.
     */
    public function getAll(): JsonResponse
    {
        $users = User::withTrashed()->with(['region', 'province', 'district', 'document_type', 'country'])->get();

        return $users->isEmpty()
            ? response()->json(['message' => 'Aún no se ha registrado ningún usuario'], Response::HTTP_OK)
            : response()->json(UserResource::collection($users), Response::HTTP_OK);
    }

    /**
     * Obtener lista paginada de usuarios.
     */
    public function getPaginated(Request $request)
    {
        $perPage = $request->query('per_page', 20);

        return User::with(['region', 'province', 'district', 'document_type', 'country'])
            ->orderBy('created_at', 'desc') // Orden descendente (más nuevos primero)
            ->paginate($perPage)
            ->appends(['per_page' => $perPage]);
    }

    /**
     * Buscar usuarios por nombre, apellidos o número de documento.
     */
    public function searchUsers(array $request)
    {
        $perPage = $request['per_page'] ?? 30;
        $searchTerm = $request['search'] ?? '';

        $query = User::with(['region', 'province', 'district', 'document_type', 'country'])
            ->orderBy('created_at', 'desc') // Orden descendente (más nuevos primero)
        ;

        if (!empty($searchTerm)) {
            $query->where(function ($query) use ($searchTerm) {
                $this->searchByTerm($query, $searchTerm);
            });
        }

        return $query->paginate($perPage)->appends([
            'per_page' => $perPage,
            'search' => $searchTerm,
        ]);
    }

    /**
     * Registrar un nuevo usuario o restaurarlo si estaba eliminado.
     */
    public function storeOrRestore(array $data, ?UploadedFile $photo = null): JsonResponse
    {
        $user = User::withTrashed()->where('document_number', $data['document_number'])->first();
        $data['password'] = Hash::make('12345');

        if ($user) {
            if ($user->trashed()) {
                $user->restore();
                $user->update($data);

                // Manejar la imagen si se proporciona
                if ($photo) {
                    $this->handleUserPhoto($user, $photo);
                }

                if (isset($data['role_id'])) {
                    $role = Role::find($data['role_id']);
                    if ($role) {
                        $user->syncRoles($role->name);
                    }
                }

                return response()->json([
                    'message' => 'El usuario fue restaurado y actualizado correctamente.',
                    'password_info' => 'La contraseña inicial para este usuario es: 12345',
                    'data' => new UserResource($user->fresh()->load([
                        'region', 'province', 'district', 'document_type', 'country'
                    ]))
                ], Response::HTTP_OK);
            }

            return response()->json(['message' => 'El usuario ya existe.'], Response::HTTP_CONFLICT);
        }

        $user = User::create($data);

        // Manejar la imagen si se proporciona
        if ($photo) {
            $this->handleUserPhoto($user, $photo);
        }

        if (isset($data['role_id'])) {
            $role = Role::find($data['role_id']);
            if ($role) {
                $user->assignRole($role->name);
            }
        }

        return response()->json([
            'message' => 'Usuario creado exitosamente.',
            'password_info' => 'La contraseña inicial para este usuario es: 12345',
            'data' => new UserResource($user->load([
                'region', 'province', 'district', 'document_type', 'country'
            ]))
        ], Response::HTTP_CREATED);
    }

    /**
     * Actualizar un usuario específico.
     */
    public function update(User $user, array $data, ?UploadedFile $photo = null): JsonResponse
    {
        $authUser = Auth::user();

        if (isset($data['new_password'])) {
            if ($authUser->id !== $user->id) {
                unset($data['new_password']);
            } else {
                $data['password'] = Hash::make($data['new_password']);
            }
            unset($data['new_password'], $data['current_password']);
        }

        $exists = User::withTrashed()
            ->where(function ($q) use ($data, $user) {
                if (isset($data['document_number'])) $q->orWhere('document_number', $data['document_number']);
                if (isset($data['email'])) $q->orWhere('email', $data['email']);
                if (isset($data['user_name'])) $q->orWhere('user_name', $data['user_name']);
            })
            ->where('id', '!=', $user->id)
            ->first();

        if ($exists) {
            return response()->json(['message' => 'Ya existe un usuario con los mismos datos.'], Response::HTTP_CONFLICT);
        }

        $roleName = null;
        if (isset($data['role'])) {
            $roleName = $data['role'];
            unset($data['role']);
        } elseif (isset($data['role_id'])) {
            $role = Role::find($data['role_id']);
            if ($role) {
                $roleName = $role->name;
            }
            unset($data['role_id']);
        }

        $user->update($data);

        // Manejar la imagen si se proporciona
        if ($photo) {
            $this->handleUserPhoto($user, $photo);
        }

        if ($roleName) {
            $user->syncRoles($roleName);
        }

        return response()->json(new UserResource($user->load([
            'region', 'province', 'district', 'document_type', 'country', 'roles'
        ])));
    }

    /**
     * Eliminar un usuario (soft delete).
     */
    public function destroy(User $user): JsonResponse
    {
        // Eliminar la imagen del usuario antes de eliminarlo
        $this->deleteUserPhoto($user);
        
        $user->delete();
        return response()->json(['message' => 'Usuario eliminado correctamente'], Response::HTTP_OK);
    }

    // ========================================
    // MÉTODOS PRIVADOS
    // ========================================

    /**
     * Manejar subida/actualización de foto de usuario
     */
    private function handleUserPhoto(User $user, UploadedFile $photo): void
    {
        try {
            // Eliminar imagen anterior si existe
            if ($user->photo_url && Storage::disk('public')->exists($user->photo_url)) {
                Storage::disk('public')->delete($user->photo_url);
            }

            // Generar nombre alfanumérico aleatorio de máximo 15 caracteres
            $fileName = $this->generateRandomFileName($photo);
            
            // Subir nueva imagen con el nombre personalizado
            $path = $photo->storeAs('images/users', $fileName, 'public');
            
            // Actualizar usuario
            $user->update(['photo_url' => $path]);

        } catch (\Exception $e) {
            // Log del error si es necesario
            Log::error('Error al manejar foto de usuario: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar foto del usuario
     */
    private function deleteUserPhoto(User $user): void
    {
        try {
            if ($user->photo_url && Storage::disk('public')->exists($user->photo_url)) {
                Storage::disk('public')->delete($user->photo_url);
                $user->update(['photo_url' => null]);
            }
        } catch (\Exception $e) {
            // Log del error si es necesario
            Log::error('Error al eliminar foto de usuario: ' . $e->getMessage());
        }
    }

    /**
     * Generar nombre de archivo alfanumérico aleatorio
     */
    private function generateRandomFileName(UploadedFile $file): string
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

    /**
     * Filtros de búsqueda por término
     */
    private function searchByTerm(Builder $query, string $searchTerm): Builder
    {
        return $query->where(function ($q) use ($searchTerm) {
            $q->where('document_number', '=', $searchTerm)
                ->orWhere('document_number', 'LIKE', "{$searchTerm}%")
                ->orWhere('name', 'LIKE', "{$searchTerm}%")
                ->orWhere('name', 'LIKE', "% {$searchTerm}%")
                ->orWhere('paternal_lastname', 'LIKE', "{$searchTerm}%")
                ->orWhere('maternal_lastname', 'LIKE', "{$searchTerm}%")
                ->orWhereRaw("CONCAT(paternal_lastname, ' ', maternal_lastname, ' ', name) LIKE ?", ["{$searchTerm}%"])
                ->orWhereRaw("CONCAT(name, ' ', paternal_lastname, ' ', maternal_lastname) LIKE ?", ["{$searchTerm}%"]);
        });
    }
}