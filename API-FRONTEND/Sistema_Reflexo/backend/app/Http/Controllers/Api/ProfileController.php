<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Services\User\ProfileService;

class ProfileController extends Controller
{
    // Corrige la inyección del servicio, asegurándote que el nombre del parámetro sea correcto
    public function __construct(private ProfileService $profileService)
    {
        $this->profileService = $profileService;
        $this->middleware('can:profile.show')->only('show');
        $this->middleware('can:profile.update')->only('update');
    }

    /**
     * Mostrar el perfil del usuario autenticado.
     */
    public function show(): JsonResponse
    {
        // Llamar al método que obtiene el perfil del usuario autenticado
        return $this->profileService->getAuthenticatedUser();
    }

    /**
     * Actualizar el perfil del usuario autenticado.
     */
    public function update(UpdateProfileRequest $request)
    {
        return $this->profileService->updateAuthenticatedUser($request->validated());
    }
}