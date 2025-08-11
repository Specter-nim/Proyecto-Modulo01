<?php

namespace App\Services\FirstLogin;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationEmail;
use App\Models\User;
use App\Models\UserVerificationCode;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
class EmailServices
{
    /**
     * Enviar el correo de verificación al usuario.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function sendVerificationEmail(User $user, int $type_email): JsonResponse
    {
        try {
            DB::transaction(function () use ($user, $type_email) {
                // Bloquea la fila del último código para evitar condiciones de carrera
                $lastToken = UserVerificationCode::where('user_id', $user->id)
                    ->orderByDesc('created_at')
                    ->lockForUpdate()
                    ->first();

                // Expirar el token activo si existe y aún no ha expirado
                if ($lastToken && $lastToken->expires_at > now()) {
                    $lastToken->update(['expires_at' => now()]);
                }

                // Crear nuevo código
                $code = random_int(100000, 999999);

                UserVerificationCode::create([
                    'user_id' => $user->id,
                    'code' => $code,
                    'expires_at' => now()->addMinutes(10),
                    'failed_attempts' => 0,
                    'locked_until' => null,
                ]);

                // Enviar correo según tipo
                switch ($type_email) {
                    case 0:
                        // Verificación de cuenta
                        Mail::to($user->email)->send(new VerificationEmail($user, $code, 'Verifica tu cuenta','emails.verification'));
                        break;
                    case 1:
                        // Recuperación de contraseña
                        Mail::to($user->email)->send(new VerificationEmail($user, $code, 'Recupera tu contraseña','emails.password-restore'));
                        break;

                    case 2:
                        $email = isset($user->new_email) ? $user->new_email : $user->email;
                        // Confirmación de cambio de correo
                        Mail::to($email)->send(new VerificationEmail($user, $code, 'Confirma tu nuevo correo','emails.email-restore'));
                        break;

                    default:
                        throw new \InvalidArgumentException("Tipo de correo inválido: $type_email");
                }
            });

            return response()->json([
                'message' => 'Código de verificación enviado con éxito.',
                'email' => isset($user->new_email)?$user->new_email:$user->email
            ], Response::HTTP_OK);

        } catch (\Throwable $e) {
            Log::error('Error al enviar correo de verificación: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'email' => isset($user->new_email)?$user->new_email:$user->email
            ]);

            return response()->json([
                'message' => 'Ocurrió un error al intentar enviar el código de verificación. Intenta nuevamente más tarde.'. $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}