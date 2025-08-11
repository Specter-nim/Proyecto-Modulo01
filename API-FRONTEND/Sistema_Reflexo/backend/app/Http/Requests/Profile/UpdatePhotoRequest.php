<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePhotoRequest extends FormRequest
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
            'photo' => [
                'nullable', // Cambiado a nullable para permitir eliminar foto
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
            'photo.image' => 'El archivo debe ser una imagen.',
            'photo.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif o webp.',
            'photo.max' => 'La imagen no debe superar los 5MB.',
            'photo.dimensions' => 'La imagen debe tener un mínimo de 100x100px y máximo de 2000x2000px.'
        ];
    }
}