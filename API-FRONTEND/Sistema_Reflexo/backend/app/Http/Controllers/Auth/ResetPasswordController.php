<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\FirstLogin\ResetPasswordServices;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\ResetPasswordRequest;

class ResetPasswordController extends Controller
{
    protected ResetPasswordServices $resetPasswordServices;

    /**
     * Create a new controller instance.
     *
     * @param  ResetPasswordServices  $resetPasswordServices
     * @return void
     */
    public function __construct(ResetPasswordServices $resetPasswordServices)
    {
        $this->resetPasswordServices = $resetPasswordServices;
    }

    public function index()
    {
        //
    }

    /**
     * Verifica si el email existe en la base de datos
     *
     * @param  ResetPasswordRequest  $request
     */
    public function show(ResetPasswordRequest $request){
        $request->validated();
        $email= $request->email;
        return $this->resetPasswordServices->verifyEmail($email);
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
