<?php

namespace Database\Seeders;

use App\Models\History;
use Illuminate\Database\Seeder;
use League\Csv\Reader;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            $csvFile = storage_path('app/csv/histories.csv');

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
                $appointments = [];
                foreach ($chunk as $row) {
                    // Verificar si el patient_id existe en la tabla 'patients'
                    if (!DB::table('patients')->where('id', $row['patient_id'])->exists()) {
                        $this->command->warn("⚠️ El patient_id {$row['patient_id']} NO existe. Saltando este registro.");
                        continue; // solo salta este registro, no corta todo el seeder
                    }
                    

                    // Si el patient_id existe, agregamos el registro
                    $appointments[] = [
                        //'id'                   => $row['id'] ?: null,
                        'patient_id'           => $row['patient_id'] ?: null,
                        'testimony'            => $row['testimony'] ?: null,
                        'private_observation'  => $row['private_observation'] ?: null,
                        'observation'          => $row['observation'] ?: null,
                        'height'               => $row['height'] ?: null,
                        'weight'               => $row['weight'] ?: null,
                        'last_weight'          => $row['last_weight'] ?: null,
                        'menstruation'         => $row['menstruation'] ?: null,
                        'diu_type'             => $row['diu_type'] ?: null,
                        'gestation'            => $row['gestation'] ?: null,
                        'created_at'           => $row['created_at'] ?: now(),
                        'updated_at'           => $row['updated_at'] ?: now(),
                        'deleted_at'           => $row['deleted_at'] ?: null,
                    ];
                }

                // Insertar todos los registros de este chunk en la base de datos
                History::insert($appointments);
            });

            // Reactivar logs de consultas (si lo necesitas)
            DB::enableQueryLog();

            $this->command->info('✅ Seeding completado con éxito. 🎉');
        } catch (Exception $e) {
            $this->command->error('❌ Error durante el seeding: ' . $e->getMessage());
        }
    }
}
