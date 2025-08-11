<?php

// app/Http/Requests/EmailRequest.php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class EmailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Obtiene las reglas de validacion que se aplican a la solicitud.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'type_email' => ['required', 'integer', 'in:0,1,2'],
            'new_email' => ['required_if:type_email,2', 'email', 'unique:users,email'],
        ];
    }

    /**
     * Consigue los mensajes de error para definir la validacion de reglas.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'type_email.required' => 'El tipo de correo es obligatorio.',
            'type_email.in' => 'El tipo de correo debe ser 0, 1 o 2.',
            'new_email.required_if' => 'El nuevo correo es obligatorio cuando el tipo es 2.',
            'new_email.email' => 'El nuevo correo debe ser una dirección válida.',
            'new_email.unique' => 'Este correo ya está en uso por otro usuario.',
        ];
    }
}

