<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Province extends Model
{
    /** @use HasFactory<\Database\Factories\ProvinceFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'region_id'
    ];
    
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
    
    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class);
    }
    
    public function therapists(): HasMany
    {
        return $this->hasMany(therapist::class);
    }

    public function districts(): HasMany
    {
        return $this->hasMany(District::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

}
