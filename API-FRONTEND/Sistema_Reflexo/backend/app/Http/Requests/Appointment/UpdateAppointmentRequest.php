<?php

namespace App\Http\Requests\Appointment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentRequest extends FormRequest
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
            'appointment_date' => 'nullable|date',
            'appointment_hour' => 'nullable|date_format:H:i',
            'ailments' => 'nullable|string|max:1000',
            'diagnosis' => 'nullable|string|max:1000',
            'surgeries' => 'nullable|string|max:1000',
            'reflexology_diagnostics' => 'nullable|string|max:1000',
            'medications' => 'nullable|string|max:255',
            'observation' => 'nullable|string|max:255',
            'initial_date' => 'nullable|date',
            'final_date' => 'nullable|date|after_or_equal:initial_date',
            'appointment_type' => 'nullable|string',
            'room' => 'nullable|integer',
            'social_benefit' => 'nullable|boolean',
            'payment_detail' => 'nullable|string|max:255',
            'payment' => 'nullable|numeric|min:0',
            'ticket_number' => 'nullable|integer',
            'appointment_status_id' => 'nullable|exists:appointment_statuses,id',
            'payment_type_id' => 'nullable|exists:payment_types,id',
            'patient_id' => 'sometimes|required|exists:patients,id',
            'therapist_id' => 'nullable|exists:therapists,id',
        ];
    }

    public function messages()
    {
        return [
            //'name.unique' => 'El estado de cita ya está registrado.',
        ];
    }
}
