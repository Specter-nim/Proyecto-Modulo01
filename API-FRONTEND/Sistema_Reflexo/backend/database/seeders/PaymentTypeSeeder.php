<?php

namespace Database\Seeders;

use App\Models\PaymentType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentTypes = [
            [
                'name' => 'YAPE',
                'description' => null
            ],
            [
                'name' => 'EFECTIVO',
                'description' => null
            ],
            [
                'name'=>'CUPÓN SIN COSTO',
                'description' =>null
            ]

        ];

        foreach ($paymentTypes as $type) {
            PaymentType::firstOrCreate(['name' => $type['name']], $type);
        }
    }
}
