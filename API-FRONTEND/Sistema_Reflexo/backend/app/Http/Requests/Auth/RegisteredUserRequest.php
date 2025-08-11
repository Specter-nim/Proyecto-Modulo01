<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisteredUserRequest extends FormRequest
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
            'document_number'   => 'required|string|max:20|unique:users,document_number',
            'name'              => 'required|string|max:255',
            'paternal_lastname' => 'required|string|max:255',
            'maternal_lastname' => 'nullable|string|max:255',
            'email'             => 'required|string|email|max:255|unique:users,email',
            'phone'             => 'nullable|string|max:100',
            'user_name'         => 'nullable|string|max:150|unique:users,user_name',
            'password'          => 'required|string|max:150',
            'last_session'      => 'nullable|date',
            'account_statement' => 'boolean',
            'document_type_id'  => 'required|exists:document_types,id',
            'country_id'        => 'nullable|exists:countries,id',
        ];
    }
}
