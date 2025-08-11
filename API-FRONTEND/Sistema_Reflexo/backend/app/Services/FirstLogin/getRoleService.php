<?php
namespace App\Services\FirstLogin;

use App\Models\UserVerificationCode;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
class getRoleService
{
    /**
     * Consulta el rol del usuario autenticado y sus permisos, y devuelve
     * los resultados en un objeto JSON.
     *
     * @param User $user El usuario autenticado.
     * @return JsonResponse Un objeto Json con los resultados.
     */
    public function getRoleUser(User $user):  JsonResponse
    {
        if (!$user) {
            return response()->json([
                'message' => 'Usuario no encontrado.'
            ], 404);
        }

        // Consulta optimizada en una sola query
        $results = DB::table('users as u')
            ->join('model_has_roles as mr', function ($join) {
                $join->on('mr.model_id', '=', 'u.id')
                     ->where('mr.model_type', '=', User::class);
            })
            ->join('roles as r', 'r.id', '=', 'mr.role_id')
            ->join('role_has_permissions as rp', 'rp.role_id', '=', 'r.id')
            ->join('permissions as p', 'p.id', '=', 'rp.permission_id')
            ->where('u.id', $user->id)
            ->select(
                'u.id as user_id',
                'r.id as role_id',
                'r.name as role_name',
                'p.name as permission_name',
                'p.detail as permission_details'
            )
            ->get();

        if ($results->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron roles o permisos para este usuario.',
                'user_id' => $user->id
            ], 404);
        }

        // Extraer datos base del rol del primer resultado
        $first = $results->first();
        $permissions = $results->map(fn($item) => [
            'name' => $item->permission_name,
            'details' => $item->permission_details
        ]);

        return response()->json([
            'name'=> $user->name . ' ' . $user->paternal_lastname . ' ' . $user->maternal_lastname,
            'user_id' => $first->user_id,
            'role_id' => $first->role_id,
            'name_role' => $first->role_name,
            'permissions' => $permissions
        ], 200);
    }
}