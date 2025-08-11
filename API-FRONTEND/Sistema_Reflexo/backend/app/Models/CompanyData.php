<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CompanyData extends Model
{
    use HasFactory;

    protected $table = 'table_company_data';

    protected $fillable = [
        'company_name',
        'company_logo',
    ];

    /**
     * Obtener la URL completa del logo
     */
    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->company_logo) {
            return null;
        }

        return Storage::url($this->company_logo);
    }

    /**
     * Verificar si el logo existe en el storage
     */
    public function hasLogo(): bool
    {
        return $this->company_logo && Storage::disk('public')->exists($this->company_logo);
    }
}