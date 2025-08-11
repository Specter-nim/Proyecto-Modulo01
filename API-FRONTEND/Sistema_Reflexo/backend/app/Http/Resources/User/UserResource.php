<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transforma el recurso en un array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'document_number' => $this->document_number,
            'photo_url' => $this->photo_url,
            'name' => $this->name,
            'paternal_lastname' => $this->paternal_lastname,
            'maternal_lastname' => $this->maternal_lastname,
            'full_name'=> "{$this->paternal_lastname} {$this->maternal_lastname} {$this->name}",
            'email' => $this->email,
            'sex' => $this->sex,
            'phone' => $this->phone,
            'user_name' => $this->user_name,

            'account_statement' => $this->account_statement,

            // Relaciones
            'region' => $this->whenLoaded('region', fn () => [
                'id' => $this->region->id ?? null,
                'name' => $this->region->name ?? null,
            ]),

            'province' => $this->whenLoaded('province', fn () => [
                'id' => $this->province->id ?? null,
                'name' => $this->province->name ?? null,
            ]),

            'district' => $this->whenLoaded('district', fn () => [
                'id' => $this->district->id ?? null,
                'name' => $this->district->name ?? null,
            ]),

            'document_type' => $this->whenLoaded('document_type', fn () => [
                'id' => $this->document_type->id ?? null,
                'name' => $this->document_type->name ?? null,
            ]),

            'country' => $this->whenLoaded('country', fn () => [
                'id' => $this->country->id ?? null,
                'name' => $this->country->name ?? null,
            ]),

            'role' => $this->roles->first() ? [
                'id' => $this->roles->first()->id,
                'name' => $this->roles->first()->name,
            ] : null,
        ];
    }
}
