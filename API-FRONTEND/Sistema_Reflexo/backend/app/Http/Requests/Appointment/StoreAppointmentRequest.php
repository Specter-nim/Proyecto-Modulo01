<?php

namespace App\Http\Requests\Appointment;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;


class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'appointment_date' => [
                'required',
                'date',
            ],
            'appointment_hour' => [
                'sometimes',
                'date_format:H:i',
            ],
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
            'patient_id' => [
                'required',
                'exists:patients,id',
                Rule::unique('appointments')
                    ->where(function ($query) {
                        return $query->where('appointment_date', $this->appointment_date)
                                     ->where('appointment_hour', $this->appointment_hour)
                                     ->whereNull('deleted_at');
                    }),
            ],
            'therapist_id' => 'nullable|exists:therapists,id',
        ];
    }

    public function messages()
    {
        return [
            'patient_id.unique' => 'Ya existe una cita para este paciente en la fecha y hora seleccionadas.',
        ];
    }
}
