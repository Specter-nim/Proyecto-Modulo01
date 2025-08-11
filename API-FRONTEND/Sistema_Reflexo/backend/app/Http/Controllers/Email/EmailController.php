<?php

namespace App\Http\Controllers\Email;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FirstLogin\EmailServices;
use App\Http\Requests\Auth\EmailRequest;
use App\Models\User;
class EmailController extends Controller
{
    protected EmailServices $emailServices;
    public function __construct(EmailServices $emailServices)
    {
        $this->emailServices = $emailServices;
    }
    public function sendVerifyCode(EmailRequest $request,User $user=null)
    {
        // Usa el usuario autenticado si existe; si no, usa el de la ruta
        $user = auth('sanctum')->user() ?? $user;

        if (!$user) {
            return response()->json([
                'message' => 'No se pudo determinar el usuario.'
            ], 400);
        }

        $typeEmail = (int) $request->type_email;

        // Solo setea new_email si aplica (tipo 2)
        if ($typeEmail === 2 && $request->filled('new_email')) {
            $user->new_email = $request->new_email;
        }

        return $this->emailServices->sendVerificationEmail($user, $typeEmail);
    }
}
