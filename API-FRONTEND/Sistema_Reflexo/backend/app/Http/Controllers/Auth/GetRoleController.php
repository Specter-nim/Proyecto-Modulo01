<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\FirstLogin\GetRoleService;
use App\Http\Resources\GetRoleResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class GetRoleController extends Controller
{
    protected GetRoleService $getRoleService;

    /**
     * Constructor.
     *
     * @param GetRoleService $getRoleService Instancia del servicio que se encarga
     *                                       de obtener el rol del usuario autenticado.
     */
    public function __construct(GetRoleService $getRoleService)
    {
        $this->getRoleService = $getRoleService;
    }

    /**
     * Consulta el rol del usuario autenticado y sus permisos, y devuelve
     * los resultados en un objeto JSON.
     * 
     * @return JsonResponse Un objeto Json con los resultados.
     */
    public function show(): JsonResponse
    {
        $user = auth()->user(); // O request()->user()
        return $this->getRoleService->getRoleUser($user);
    }
}