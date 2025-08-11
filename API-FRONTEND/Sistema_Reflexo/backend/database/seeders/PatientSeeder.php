<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Patient;
use League\Csv\Reader;
use Exception;
use Illuminate\Support\Facades\DB;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            $csvFile = storage_path('app/csv/patients.csv');
            //dd($csvFile, file_exists($csvFile));

            if (!file_exists($csvFile)) {
                $this->command->error("❌ Archivo CSV no encontrado: {$csvFile}");
                return;
            }

            $csv = Reader::createFromPath($csvFile, 'r');
            $csv->setDelimiter(';');
            $csv->setHeaderOffset(0);

            // Crear un generador de filas para procesar el CSV sin cargar todo a la vez
            $csvData = $csv->getRecords();

            // Desactivar logs de consultas
            DB::disableQueryLog();

            // Procesar en chunks de 1000 registros
            collect($csvData)->chunk(1000)->each(function ($chunk) {
                $patients = [];
                foreach ($chunk as $row) {
                    $patients[] = [
                        'id'   => $row['id'] ?: null,
                        'document_number'   => $row['document_number'] ?: null,
                        'document_type_id'  => $row['document_type_id'] ?: null,
                        'paternal_lastname' => $row['paternal_lastname'],
                        'maternal_lastname' => $row['maternal_lastname'] ?: null,
                        'name'              => $row['name'],
                        'birth_date'        => $row['birth_date'] ?: null,
                        'sex'               => $row['sex'] ?: null,
                        'primary_phone'     => $row['primary_phone'] ?: null,
                        'secondary_phone'   => $row['secondary_phone'] ?: null,
                        'email'             => $row['email'] ?: null,
                        'ocupation'         => $row['ocupation'] ?: null,
                        'health_condition'  => $row['health_condition'] ?: null,
                        'address'           => $row['address'] ?: null,
                        'region_id'         => $row['region_id'] == 0 ? null : $row['region_id'],
                        'province_id'       => $row['province_id'] == 0 ? null : $row['province_id'],
                        'district_id'       => $row['district_id'] == 0 ? null : $row['district_id'],
                        'country_id'        => $row['country_id'] == 0 ? null : $row['country_id'],
                        'created_at'        => $row['created_at'] ?: now(),
                        'updated_at'        => $row['updated_at'] ?: now(),
                        'deleted_at'        => $row['deleted_at'] ?: null,
                    ];
                }

                // Insertar todos los registros de este chunk en la base de datos
                Patient::insert($patients);
            });

            // Reactivar logs de consultas (si lo necesitas)
            DB::enableQueryLog();

            $this->command->info('✅ Seeding completado con éxito. 🎉');
        } catch (Exception $e) {
            $this->command->error('❌ Error durante el seeding: ' . $e->getMessage());
        }
    }
}
