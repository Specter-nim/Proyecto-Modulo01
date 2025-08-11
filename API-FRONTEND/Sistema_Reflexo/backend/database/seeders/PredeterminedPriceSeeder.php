<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PredeterminedPrice;

class PredeterminedPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'Servicio básico', 'price' => 30.00],
            ['name' => 'Servicio estándar', 'price' => 40.00],
            ['name' => 'Servicio premium', 'price' => 50.00],
        ];

        foreach ($data as $item) {
            PredeterminedPrice::create($item);
        }
    }
}
