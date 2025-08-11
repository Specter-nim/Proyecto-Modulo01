<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisteredUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class RegisteredUserController extends Controller
{
    /**
     * Store a newly created user in storage.
     *
     * @param  \App\Http\Requests\Auth\RegisteredUserRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(RegisteredUserRequest $request)
{
    $user = User::create([
        'document_number'   => $request->document_number,
        'name'              => $request->name,
        'paternal_lastname' => $request->paternal_lastname,
        'maternal_lastname' => $request->maternal_lastname,
        'email'             => $request->email,
        'phone'             => $request->phone,
        'user_name'         => $request->user_name,
        'password'          => Hash::make($request->password),
        'last_session'      => $request->last_session,
        'account_statement' => 1, 
        'document_type_id'  => $request->document_type_id,
        'country_id'        => $request->country_id,
    ]);

    $user->assignRole('Admin');

    return new JsonResponse(
        data: $user,
        status: Response::HTTP_CREATED
    );
}

}
