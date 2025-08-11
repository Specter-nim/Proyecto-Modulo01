<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Country;
use League\Csv\Reader;
use Exception;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            $csvFile = public_path('csv/countries.csv');

            $csv = Reader::createFromPath($csvFile, 'r');
            $csv->setDelimiter(';');
            $csv->setHeaderOffset(0);

            foreach ($csv as $row) {
                Country::create([
                    'name' => $row['name'],
                    'phone_code' => $row['phone_code'] ?? null,
                    'ISO2' => $row['ISO2'] ?? null,
                ]);
            }

            $this->command->info('✅ Seeding de países completado con éxito. 🎉');
        } catch (Exception $e) {
            $this->command->error('❌ Error durante el seeding: ' . $e->getMessage());
        }
    }
}
