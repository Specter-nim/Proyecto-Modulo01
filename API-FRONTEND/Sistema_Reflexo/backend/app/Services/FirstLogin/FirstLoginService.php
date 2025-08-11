<?php

namespace App\Services\FirstLogin;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class FirstLoginService
{
    /**
     * Verifica si el usuario ya ha iniciado sesión con anterioridad.
     *
     * Si el usuario ya ha iniciado sesión, se devuelve un token de acceso y se
     * indica que no es un primer login.
     *
     * Si el usuario no ha iniciado sesión, se devuelve un JSON con un flag
     * "first_login" en true y se envía un código de verificación al correo
     * electrónico del usuario.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function verifyFirstLogin(User $user): JsonResponse
    {
        try {
            if ($user->last_session) {
                $token = $user->createToken('token')->plainTextToken;

                return response()->json([
                    'first_login' => false,
                    'message' => 'Login exitoso',
                    'token' => $token,
                    'role'=> $user->roles->first()?->id
                ], Response::HTTP_OK);
            }

            // Primer login
            return response()->json([
                'first_login' => true,
                'user_id' => $user->id,
                'message' => 'Se enviará un código de verificación'
            ], Response::HTTP_OK);

        } catch (\Throwable $e) {
            Log::error('Error al verificar el primer login', [
                'user_id' => $user->id ?? null,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => true,
                'message' => 'Error interno en la verificación de inicio de sesión.',
                'exception' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}