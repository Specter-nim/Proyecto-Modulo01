<?php

namespace App\Http\Requests\Appointment;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AppointmentsPaginatedByDateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date' => 'sometimes|date_format:Y-m-d',
            'per_page' => 'required|integer|min:1|max:100'
        ];
    }

    public function messages(): array
    {
        return [
            'date.date_format' => 'La fecha debe tener el formato YYYY-MM-DD.',
            'per_page.integer' => 'El número de elementos por página debe ser un número entero.',
            'per_page.required' => 'Es necesario especificar el número de elementos por página',
            'per_page.min' => 'El número mínimo por página es 1.',
            'per_page.max' => 'El número máximo por página es 100.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ],422)    
        );
        
    }
}
