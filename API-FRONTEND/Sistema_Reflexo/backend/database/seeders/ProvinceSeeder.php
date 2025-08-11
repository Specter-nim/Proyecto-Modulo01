<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Province;
use League\Csv\Reader;
use Exception;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            $csvFile = public_path('csv/provinces.csv');

            $csv = Reader::createFromPath($csvFile, 'r');
            $csv->setDelimiter(';');
            $csv->setHeaderOffset(0);

            foreach ($csv as $row) {
                Province::create([
                    'id' => $row['id'],
                    'name' => $row['name'] ?? null,
                    'region_id' => $row['region_id'] ?? null,
                ]);
            }

            $this->command->info('✅ Seeding de países completado con éxito. 🎉');
        } catch (Exception $e) {
            $this->command->error('❌ Error durante el seeding: ' . $e->getMessage());
        }
    }
}
