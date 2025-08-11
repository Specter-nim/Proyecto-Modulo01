<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Region;
use League\Csv\Reader;
use Exception;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            $csvFile = public_path('csv/regions.csv');

            $csv = Reader::createFromPath($csvFile, 'r');
            $csv->setDelimiter(';');
            $csv->setHeaderOffset(0);

            foreach ($csv as $row) {
                Region::create([
                    'id'=>$row['id'],
                    'name' => $row['name'],
                ]);
            }

            $this->command->info('✅ Seeding de regiones completado con éxito. 🎉');
        } catch (Exception $e) {
            $this->command->error('❌ Error durante el seeding: ' . $e->getMessage());
        }
    }
}
