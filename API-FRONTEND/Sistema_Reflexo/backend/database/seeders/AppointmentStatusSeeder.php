<?php

namespace Database\Seeders;

use App\Models\AppointmentStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppointmentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $appointmentStatuses = [
            [
                'name' => 'PENDIENTE',
                'description' => null
            ],
            [
                'name' => 'COMPLETADO',
                'description' => null
            ],
        ];
    
        foreach ($appointmentStatuses as $type) {
            AppointmentStatus::firstOrCreate(['name' => $type['name']], $type);
        }
    }
}
