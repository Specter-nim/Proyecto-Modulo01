<?php

namespace App\Http\Controllers\Auth;

use App\Models\UserVerificationToken;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\VerificationRequest;
use App\Services\FirstLogin\VerificationServices;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
class VerificationController extends Controller
{
    protected VerificationServices $verificationServices;

    public function __construct(VerificationServices $verificationServices)
    {
        $this->verificationServices = $verificationServices;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Valida el codigo de verificacion.
     *
     * Este metodo revisa la autenticacion del usuario
     * y valida el codigo de verificacion recibido en el request.
     *
     * @param VerificationRequest $verificationRequest The request containing the verification code.
     * @param User $user The user whose verification code is being validated.
     *
     */

    public function validateCode(VerificationRequest $verificationRequest, User $user)
    {
        $user = auth()->user() ?? $user;

        if (!$user) {
            return response()->json(['message' => 'No se pudo determinar el usuario.'], 400);
        }

        $code = $verificationRequest->code;
        return $this->verificationServices->verifyCode($user, $code);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

}
