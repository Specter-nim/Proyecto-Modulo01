<?php

namespace App\Services\User;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\User\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileService
{
    /**
     * Obtener el perfil del usuario autenticado con sus relaciones.
     */
    public function getAuthenticatedUser(): JsonResponse
    {
        $user = Auth::user()->load(['region', 'province', 'district', 'document_type', 'country']);
        return response()->json(new UserResource($user), Response::HTTP_OK);
    }

    /**
     * Actualizar datos del usuario autenticado.
     */
    public function updateAuthenticatedUser(array $data): JsonResponse
    {
        $user = Auth::user();

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return response()->json(new UserResource($user->fresh()->load(['region', 'province', 'district', 'document_type', 'country'])), Response::HTTP_OK);
    }
}