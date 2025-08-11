<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UploadImageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Manejar autorización en el middleware del controlador
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'logo' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:5120', // 2MB máximo
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
            'logo.image' => 'El archivo debe ser una imagen jpeg, png, jpg, gif o webp.',
            'logo.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif o webp.',
            'logo.max' => 'La imagen no debe superar los 5MB.',
            'logo.dimensions' => 'La imagen debe tener un mínimo de 100x100px y máximo de 2000x2000px.'
        ];
    }
}