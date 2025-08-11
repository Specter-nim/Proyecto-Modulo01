<?php
namespace App\Services\FirstLogin;

use App\Models\UserVerificationCode;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class ResetPasswordServices
{
    /**
     * Verifica si el email esta en la base de datos.
     *
     * @param string $email
     * @return JsonResponse
     */
    public function verifyEmail(string $email): JsonResponse
    {
        // Encuentra el usuario que esta asociado alemail
        $user = User::where('email', $email)->first();

        if (!$user) {
            // Regresa error si el usuario no existe
            return response()->json([
                'status' => false,
                'message' => 'No se encontró un usuario con ese correo.'
            ]);
        }

        // Regresa un mensaje de correo verificado
        return response()->json([
            'user_id'=>$user->id,
            'status' => true,
            'message' => 'Correo verificado correctamente.'
        ]);
    }
}