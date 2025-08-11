<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Therapist;
use League\Csv\Reader;
use Exception;

class TherapistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            $csvFile = storage_path('app/csv/therapists.csv');

            if (!file_exists($csvFile)) {
                $this->command->error("❌ Archivo CSV no encontrado: {$csvFile}");
                return;
            }

            $csv = Reader::createFromPath($csvFile, 'r');
            $csv->setDelimiter(';');
            $csv->setHeaderOffset(0);

            $csvData = iterator_to_array($csv);

            collect($csvData)->chunk(20)->each(function ($chunk) {
                $data = collect($chunk)->map(function ($row) {
                    return [
                        'id'   => $row['id'] ?: null,
                        'code'              => $row['code'] ?: null,
                        'document_number'   => $row['document_number'] ?: null,
                        'document_type_id'  => $row['document_type_id'] ?: null,
                        'paternal_lastname' => $row['paternal_lastname'],
                        'maternal_lastname' => $row['maternal_lastname'] ?: null,
                        'name'              => $row['name'],
                        'personal_reference' => $row['personal_reference'] ?: null,
                        'birth_date'        => $row['birth_date'] ?: null,
                        'sex'               => $row['sex'] ?: null,
                        'primary_phone'     => $row['primary_phone'] ?: null,
                        'secondary_phone'   => $row['secondary_phone'] ?: null,
                        'email'             => $row['email'] ?: null,
                        'address'           => $row['address'] ?: null,
                        'country_id'        => $row['country_id'] == 0 ? null : $row['country_id'],
                        'district_id'       => $row['district_id'] == 0 ? null : $row['district_id'],
                        'province_id'       => $row['province_id'] == 0 ? null : $row['province_id'],
                        'region_id'         => $row['region_id'] == 0 ? null : $row['region_id'],
                        'created_at'        => $row['created_at'] ?: now(),
                        'updated_at'        => $row['updated_at'] ?: now(),
                        'deleted_at'        => $row['deleted_at'] ?: null,
                    ];
                });

                Therapist::insert($data->toArray());
            });

            $this->command->info('✅ Seeding de terapeutas completado con éxito. 🎉');
        } catch (Exception $e) {
            $this->command->error('❌ Error durante el seeding: ' . $e->getMessage());
        }
    }
}
