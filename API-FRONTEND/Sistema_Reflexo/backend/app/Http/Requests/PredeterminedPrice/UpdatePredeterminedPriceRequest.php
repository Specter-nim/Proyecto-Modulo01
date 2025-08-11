<?php

namespace App\Http\Requests\PredeterminedPrice;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePredeterminedPriceRequest extends FormRequest
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
            'name' => 'sometimes|string|max:255|unique:document_types,name,',
            'price' => 'sometimes|integer'
        ];
    }
    public function messages()
    {
        return [
            'name.unique' => 'El precio predeterminado ya está registrado.',
        ];
    }
}
