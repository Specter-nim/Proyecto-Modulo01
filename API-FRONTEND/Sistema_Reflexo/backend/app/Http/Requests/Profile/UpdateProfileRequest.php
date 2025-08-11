<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determinar si el usuario está autorizado para realizar esta solicitud.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Obtenga las reglas de validación que se aplican a la solicitud.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = Auth::id(); // usuario autenticado

        return [
            'document_number' => [
                'sometimes',
                'string',
                'max:20',
                Rule::unique('users', 'document_number')->ignore($userId)->whereNull('deleted_at'),
            ],
            'photo_url' => ['nullable', 'string', 'url'],
            'name' => 'sometimes|string|max:255',
            'paternal_lastname' => 'sometimes|string|max:255',
            'maternal_lastname' => 'sometimes|nullable|string|max:255',
            'email' => [
                'sometimes',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId)->whereNull('deleted_at'),
            ],
            'sex' => 'sometimes|string|size:1',
            'phone' => 'sometimes|nullable|string|max:100',
            'user_name' => [
                'sometimes',
                'nullable',
                'string',
                'max:150',
                Rule::unique('users', 'user_name')->ignore($userId)->whereNull('deleted_at'),
            ],
            'current_password' => 'required_with:password|string',
            'password' => 'nullable|string|min:8',
            'document_type_id' => 'sometimes|exists:document_types,id',
            'country_id' => 'sometimes|nullable|exists:countries,id',
        ];
    }

    /**
     * Personalizar los mensajes de error para las reglas de validación.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'document_number.unique' => 'El número de documento ya está registrado.',
            'email.unique' => 'El correo electrónico ya está registrado.',
            'user_name.unique' => 'El nombre de usuario ya está en uso.',
        ];
    }
}