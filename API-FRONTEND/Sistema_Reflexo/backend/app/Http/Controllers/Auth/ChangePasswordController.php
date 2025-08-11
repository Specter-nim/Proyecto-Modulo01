<?php

namespace App\Http\Controllers\Auth;

use App\Models\user;
use App\Http\Controllers\Controller;
use App\Services\FirstLogin\ChangePasswordService;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\ChangePasswordRequest;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\ValidatePasswordRequest;

class ChangePasswordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected ChangePasswordService $changePasswordService;

    /**
     * Crea una nueva instancia de controlador.
     *
     * @param  \App\Services\FirstLogin\ChangePasswordService  $changePasswordService
     * @return void
     */
    public function __construct(ChangePasswordService $changePasswordService)
    {
        $this->changePasswordService = $changePasswordService;
        $this->middleware('can:change-password.update')->only('update');
        $this->middleware('auth:sanctum')->only('validatePassword');
    }
    public function index()
    {
        //
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

    /**
     * Display the specified resource.
     */
    public function show(user $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(user $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ChangePasswordRequest $request)
    {
        $user = $request->user(); // Usuario autenticado por Sanctum
        return $this->changePasswordService->changePassword($user, $request->password);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(user $user)
    {
        //
    }

    public function validatePassword(ValidatePasswordRequest $request)
    {
        $user = $request->user();
        $isValid = Hash::check($request->current_password, $user->password);

        if ($isValid) {
            return response()->json([
                'message' => 'La contraseña actual es válida.',
                'status' => true,
            ]);
        }

        return response()->json([
            'message' => 'La contraseña actual es incorrecta.',
            'status' => false,
        ], 422);
    }
}
