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
class ChangePasswordService
{
    /**
     * Actualiza la contraseña del usuario y la marca como cambiada.
     * 
     * @param User $user El usuario que cambia la contraseña
     * @param string $password La nueva contraseña del usuario
     * 
     * @return array Un array con la respuesta, puede contener:
     *               ['password_change' => true, 'message' => 'Contraseña cambiada correctamente']
     *               ['password_change' => false, 'message' => 'Error inesperado al cambiar la contraseña.', 'exception' => $e->getMessage()]
     */
    public function changePassword(User $user, string $password): array
    {
        DB::beginTransaction(); // Inicia la transacción

        try {

            // Actualizar la contraseña
            $user->password = Hash::make($password); // También puedes usar bcrypt()
            $user->password_change = true; // Marcar como cambiada
            $user->last_session= now();
            $user->save();

            DB::commit(); // Confirmar los cambios

            return[
                'password_change' => true,
                'message' => "Contraseña cambiada correctamente"
            ];
            
        } catch (\Throwable $e) {
            DB::rollBack(); // Revertir si hay un fallo

            Log::error('Error al cambiar contraseña', [
                'user_id' => $user->id,
                'password_change' => false,
                'error' => $e->getMessage(),
            ]);

            return [
                'status' => false,
                'password_change' => false,
                'message' => 'Error inesperado al cambiar la contraseña.',
                'exception' => $e->getMessage()
            ];
        }
    }
}