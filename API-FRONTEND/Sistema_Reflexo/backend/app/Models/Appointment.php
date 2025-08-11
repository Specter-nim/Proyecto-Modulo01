<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    /** @use HasFactory<\Database\Factories\AppointmentFactory> */
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'appointment_date',
        'appointment_hour',
        'ailments',
        'diagnosis',
        'surgeries',
        'reflexology_diagnostics',
        'medications',
        'observation',
        'initial_date',
        'final_date',
        'appointment_type',
        'room',
        'social_benefit',
        'payment_detail',
        'payment',
        'ticket_number',
        'appointment_status_id',
        'payment_type_id',
        'patient_id',
        'therapist_id'
    ];

    public function paymentType():BelongsTo
    {
        return $this->belongsTo(PaymentType::class);
    }
    public function appointmentStatus():BelongsTo
    {
        return $this->belongsTo(AppointmentStatus::class);
    }

    public function patient():BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function therapist():BelongsTo
    {
        return $this->belongsTo(Therapist::class);
    }
}
