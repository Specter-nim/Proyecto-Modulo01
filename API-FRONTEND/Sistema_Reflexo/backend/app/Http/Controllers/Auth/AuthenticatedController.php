<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\FirstLogin\FirstLoginService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
class AuthenticatedController extends Controller
{
    protected FirstLoginService $firstLoginService;

    /**
     * Inicializa el servicio de primera sesión.
     *
     * @param  FirstLoginService  $firstLoginService
     */
    public function __construct(FirstLoginService $firstLoginService)
    {
        $this->firstLoginService = $firstLoginService;
    }
    /**
     * Verifica las credenciales de autenticación y devuelve un token de acceso
     * si el usuario es nuevo o no ha completado el proceso de verificación.
     *
     * @param  LoginRequest  $request
     * @return JsonResponse
     */
    public function store(LoginRequest $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Credenciales inválidas.'], Response::HTTP_UNAUTHORIZED);
        }

        $user = $request->user();
        $user->tokens()->delete(); // Eliminar tokens previos

        // Lógica delegada completamente al servicio
        return $this->firstLoginService->verifyFirstLogin($user);
    }

    /**
     * Revoca el token de acceso actual del usuario autenticado, cerrando la sesión.
     *
     * @param  Request  $request
     * @return JsonResponse
     */

    /**
     * Cierra la sesión del usuario autenticado, revocando su token de acceso actual.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function destroy(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return new JsonResponse([
            'message' => 'Cerró sesión exitosamente.',
        ], Response::HTTP_OK);
    }
}
