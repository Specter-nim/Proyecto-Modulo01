<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ValidatePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Permitir todas las solicitudes autenticadas
    }

    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required' => 'Debes ingresar tu contraseña actual.',
        ];
    }
}