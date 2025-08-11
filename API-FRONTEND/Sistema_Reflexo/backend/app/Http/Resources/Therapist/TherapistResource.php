<?php
namespace App\Http\Resources\Therapist;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TherapistResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'document_number' => $this->document_number,
            'full_name' => "{$this->paternal_lastname} {$this->maternal_lastname}, {$this->name}",
            'name' => $this->name,
            'paternal_lastname' => $this->paternal_lastname,
            'maternal_lastname' => $this->maternal_lastname,
            'personal_reference' => $this->personal_reference,
            'birth_date' => $this->birth_date,
            'sex' => $this->sex,
            'primary_phone' => $this->primary_phone,
            'secondary_phone' => $this->secondary_phone,
            'email' => $this->email,
            'address' => $this->address,
            'region' => $this->region?->id,
            'province' => $this->province?->id,
            'district' => $this->district?->id,
            'document_type' => $this->documentType?->id,
            'country' => $this->country?->name,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}