<?php
namespace App\Services\FirstLogin;

use App\Models\UserVerificationCode;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class VerificationServices
{
    /**
     * Verifica un código de verificación de usuario.
     *
     * Verifica que el código sea válido, no esté expirado, no esté bloqueado por
     * intentos fallidos y coincide con el código almacenado en la base de datos.
     *
     * Si el código es válido, se devuelve un array con dos claves:
     * - 'valid' : booleano que indica que el código es válido.
     * - 'message' : mensaje de éxito.
     *
     * Si el código no es válido, se devuelve un array con dos claves:
     * - 'valid' : booleano que indica que el código no es válido.
     * - 'message' : mensaje de error.
     *
     * @param User $user El usuario que se está verificando.
     * @param string $code El código ingresado por el usuario.
     *
     * @return array Un array asociativo con dos claves: 'valid' y 'message'.
     */
    public function verifyCode(User $user, string $code): array
    {
        
        $latestcode = UserVerificationCode::where('user_id', $user->id)
            ->latest()
            ->lockForUpdate()
            ->first();

        
        $verificacion=$this->validationVerify($latestcode,$user,$code);

        if ($verificacion['valid']) {
             return $this->handleSuccessfulVerification($user, $latestcode);
        }

        return $verificacion;
    }

    /**
     * Valida un código de verificación de usuario.
     *
     * Se verifica que el código sea válido, no esté expirado, no esté bloqueado por
     * intentos fallidos y coincide con el código almacenado en la base de datos.
     *
     * @param ?UserVerificationCode $verifycode El código a verificar.
     * @param User $user El usuario que se está verificando.
     * @param string $code El código ingresado por el usuario.
     *
     * @return array Un array asociativo con dos claves: 'valid' y 'message'. 'valid'
     *     es un booleano que indica si el código es válido o no. 'message' es un
     *     mensaje de error o de éxito.
     */
    private function validationVerify(?UserVerificationCode $verifycode, User $user, $code): array
    {
        $response = [
            'valid' => true,
            'message' => "Código validado"
        ];

        if (!$verifycode) {
            $this->incrementFailedAttempts($user);
            return $this->invalidResponse('Code_not_found', 'No se encontró un código válido para este usuario.');
        }

        if ($verifycode->code !== $code) {
            $this->incrementFailedAttempts($user);
            return $this->invalidResponse('Code_mismatch', 'El código ingresado no coincide.');
        }

        if ($verifycode->locked_until && now()->lt($verifycode->locked_until)) {
            return $this->invalidResponse('Code_locked', 'Demasiados intentos fallidos. Intenta nuevamente en 10 minutos.');
        }

        if ($verifycode->isExpired()) {
            $verifycode->delete(); // Eliminar código expirado
            $this->incrementFailedAttempts($user);
            return $this->invalidResponse('Code_expired', 'El código ha expirado.');
        }

        return $response;
    }

    /**
     * Maneja toda la lógica para una verificación exitosa.
     */
    private function handleSuccessfulVerification(User $user, UserVerificationCode $userVerificationCode): array
    {
        DB::beginTransaction();
        try {
            // Verificar si ya tiene un token activo
            $existingToken = $user->tokens()
                ->where('name', 'token')
                ->first();

            // Eliminar todos los códigos de verificación (por seguridad)
            $user->verificationCode()->delete();

            // Puedes actualizar algún campo del usuario si quieres registrar el primer login, etc.
            $user->save();

            // Si ya tenía un token, devolverlo y no generar uno nuevo
            if ($existingToken) {
                DB::commit();

                return [
                    'valid' => true,
                    'message' => 'Código válido. Ya tiene un token activo.'
                ];
            }

            // Crear nuevo token si no existía
            $token = $user->createToken('token')->plainTextToken;

            DB::commit();

            return [
                'token' => $token,
                'valid' => true,
                'message' => 'Código válido.',
                'role'=> $user->roles->first()?->id
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error al verificar código', [
                'user_id' => $user->id,
                'code' => $userVerificationCode->code,
                'error_message' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'stack_trace' => $e->getTraceAsString(),
            ]);

            return $this->invalidResponse('internal_error', 'Error interno al procesar el código.', $e->getMessage());
        }
        
    }

    /**
     * Incrementa intentos fallidos y bloquea si se supera el límite.
     */
    private function incrementFailedAttempts(User $user): void
    {
        $userVerificationCode = UserVerificationCode::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($userVerificationCode) {
            $userVerificationCode->failed_attempts += 1;

            if ($userVerificationCode->failed_attempts >= 3) {
                $userVerificationCode->locked_until = now()->addMinutes(10);
            }

            $userVerificationCode->save();
        }
    }

    /**
     * Retorna una estructura uniforme para errores.
     */
    private function invalidResponse(string $reason, string $message, string $errorMessage = null): array
    {
        $response = [
            'valid' => false,
            'reason' => $reason,
            'message' => $message
        ];

        if ($errorMessage) {
            $response['error_message'] = $errorMessage;
        }

        return $response;
    }
}