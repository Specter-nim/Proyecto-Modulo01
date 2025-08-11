<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user')->id;

        return [
            'name' => 'sometimes|required|string|max:255',
            'paternal_lastname' => 'sometimes|required|string|max:255',
            'maternal_lastname' => 'sometimes|required|string|max:255',
            'document_number' => [
                'sometimes',
                'required',
                'string',
                Rule::unique('users', 'document_number')->ignore($userId)
            ],
            'email' => [
                'sometimes',
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($userId)
            ],
            'sex' => 'sometimes|string|size:1',
            'phone' => 'sometimes|nullable|string|max:100',
            'user_name' => [
                'sometimes',
                'required',
                'string',
                Rule::unique('users', 'user_name')->ignore($userId)
            ],
            'address' => 'nullable|string|max:500',
            'birth_date' => 'nullable|date',
            
            // Campos de contraseña
            'current_password' => 'sometimes|required_with:new_password|string',
            'new_password' => 'sometimes|required|string|min:6|confirmed',
            'new_password_confirmation' => 'sometimes|required_with:new_password|string',
            
            // Relaciones opcionales
            'document_type_id' => 'nullable|exists:document_types,id',
            'region_id' => 'nullable|exists:regions,id',
            'province_id' => 'nullable|exists:provinces,id',
            'district_id' => 'nullable|exists:districts,id',
            'country_id' => 'nullable|exists:countries,id',
            'role_id' => 'nullable|exists:roles,id',
            'role' => 'nullable|string|exists:roles,name',
            
            // Imagen opcional
            'photo' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:5120', // 5MB máximo
                'dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'paternal_lastname.required' => 'El apellido paterno es obligatorio.',
            'maternal_lastname.required' => 'El apellido materno es obligatorio.',
            'document_number.required' => 'El número de documento es obligatorio.',
            'document_number.unique' => 'Ya existe un usuario con este número de documento.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'El email debe tener un formato válido.',
            'email.unique' => 'Ya existe un usuario con este email.',
            'user_name.required' => 'El nombre de usuario es obligatorio.',
            'user_name.unique' => 'Ya existe un usuario con este nombre de usuario.',
            
            // Mensajes para contraseñas
            'current_password.required_with' => 'La contraseña actual es obligatoria para cambiar la contraseña.',
            'new_password.required' => 'La nueva contraseña es obligatoria.',
            'new_password.min' => 'La nueva contraseña debe tener al menos 6 caracteres.',
            'new_password.confirmed' => 'La confirmación de la nueva contraseña no coincide.',
            'new_password_confirmation.required_with' => 'La confirmación de la nueva contraseña es obligatoria.',
            
            // Mensajes para la imagen
            'photo.image' => 'El archivo debe ser una imagen.',
            'photo.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif o webp.',
            'photo.max' => 'La imagen no debe superar los 5MB.',
            'photo.dimensions' => 'La imagen debe tener un mínimo de 100x100px y máximo de 2000x2000px.'
        ];
    }
}