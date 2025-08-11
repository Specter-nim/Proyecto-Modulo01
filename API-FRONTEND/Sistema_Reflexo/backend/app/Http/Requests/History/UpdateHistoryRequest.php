<?php

namespace App\Http\Requests\History;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateHistoryRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {

        return [
            'testimony' => 'nullable|boolean',
            'private_observation' => 'nullable|string|max:255',
            'observation' => 'nullable|string|max:255',
            'height' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'last_weight' => 'nullable|numeric',
            'menstruation' => 'nullable|boolean',
            'diu_type' => 'nullable|string|max:255',
            'gestation' => 'nullable|boolean',
            'patient_id' => 'nullable|exists:patients,id'
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'testimony.boolean' => 'El campo testimonio debe ser verdadero o falso.',
            'private_observation.string' => 'La observación privada debe ser una cadena de texto.',
            'observation.string' => 'La observación debe ser una cadena de texto.',
            'height.numeric' => 'La altura debe ser un número.',
            'weight.numeric' => 'El peso debe ser un número.',
            'last_weight.numeric' => 'El último peso debe ser un número.',
            'menstruation.boolean' => 'El campo menstruación debe ser verdadero o falso.',
            'diu_type.string' => 'El tipo de DIU debe ser una cadena de texto.',
            'gestation.boolean' => 'El campo gestación debe ser verdadero o falso.',
            'patient_id.exists' => 'El paciente seleccionado no existe.'
        ];
    }
}
