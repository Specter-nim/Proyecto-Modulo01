<?php

namespace Database\Seeders;

use App\Models\District;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use League\Csv\Reader;
use Exception;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            $csvFile =  public_path('csv/districts.csv');

            $csv = Reader::createFromPath($csvFile, 'r');
            $csv->setDelimiter(';');
            $csv->setHeaderOffset(0);

            foreach ($csv as $row) {
                District::create([
                    'id' => $row['id'],
                    'name' => $row['name'] ?? null,
                    'province_id' => $row['province_id'] ?? null,
                ]);
            }

            $this->command->info('✅ Seeding de provincias completado con éxito. 🎉');
        } catch (Exception $e) {
            $this->command->error('❌ Error durante el seeding: ' . $e->getMessage());
        }
    }
}
