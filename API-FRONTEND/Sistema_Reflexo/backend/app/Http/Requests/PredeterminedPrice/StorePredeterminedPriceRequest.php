<?php

namespace App\Http\Requests\PredeterminedPrice;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePredeterminedPriceRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('predetermined_prices', 'name')->whereNull('deleted_at'), // Ignora los eliminados
            ],
            'price' => 'nullable|numeric|min:0',
        ];
    }
    public function messages()
    {
        return [
            'name.unique' => 'El precio predeterminado ya está registrado.',
        ];
    }
}
