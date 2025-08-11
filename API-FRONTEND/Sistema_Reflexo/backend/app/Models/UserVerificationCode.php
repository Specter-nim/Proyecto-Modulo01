<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class UserVerificationCode extends Model
{
    use HasFactory;
        // Especificar el nombre de la tabla
    protected $table = 'user_verification_code';
    protected $fillable = [
        'user_id',
        'code',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    // Relación con User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Validar si el token ya expiró
    public function isExpired(): bool
    {
        return $this->expires_at->isPast() ?? true;
    }
}
