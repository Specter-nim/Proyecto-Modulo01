<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StatisticsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'terapeutas' => $this['terapeutas'],
            'tipos_pago' => $this['tipos_pago'],
            'metricas' => $this['metricas'],
            'ingresos' => $this['ingresos'],
            'sesiones' => $this['sesiones'],
            'tipos_pacientes' => $this['tipos_pacientes'],
        ];
    }
}
