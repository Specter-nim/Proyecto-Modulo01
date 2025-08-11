<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
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
        return [
            // Campos obligatorios del usuario
            'name' => 'required|string|max:255',
            'paternal_lastname' => 'required|string|max:255',
            'maternal_lastname' => 'nullable|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->whereNull('deleted_at'),
            ],
            'sex' => 'required|string|size:1',
            'phone' => 'nullable|string|max:100',
            'user_name' => [
                'nullable',
                'string',
                'max:150',
                Rule::unique('users', 'user_name')->whereNull('deleted_at'),
            ],
            'document_number' => [
                'required',
                'string',
                Rule::unique('users', 'document_number')
            ],
            //'password' => 'required|string|min:8|confirmed',
            'document_type_id' => 'required|exists:document_types,id',
            'country_id' => 'nullable|exists:countries,id',
            'role_id' => 'nullable|exists:roles,id',
            
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
            
            // Mensajes para la imagen
            'photo.image' => 'El archivo debe ser una imagen.',
            'photo.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif o webp.',
            'photo.max' => 'La imagen no debe superar los 5MB.',
            'photo.dimensions' => 'La imagen debe tener un mínimo de 100x100px y máximo de 2000x2000px.'
        ];
    }
}