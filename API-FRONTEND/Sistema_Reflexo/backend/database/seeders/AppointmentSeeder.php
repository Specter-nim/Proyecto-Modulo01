<?php

namespace Database\Seeders;

use App\Models\Appointment;
use Illuminate\Database\Seeder;
use League\Csv\Reader;
use Exception;
use Illuminate\Support\Facades\DB;


class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            $csvFile = storage_path('app/csv/appointments.csv');

            if (!file_exists($csvFile)) {
                $this->command->error("❌ Archivo CSV no encontrado: {$csvFile}");
                return;
            }

            $csv = Reader::createFromPath($csvFile, 'r');
            $csv->setDelimiter(';');
            $csv->setHeaderOffset(0);

            $csvData = $csv->getRecords();

            DB::disableQueryLog();

            collect($csvData)->chunk(1000)->each(function ($chunk) {
                $appointments = [];
                foreach ($chunk as $row) {
                    $appointments[] = [
                        'id'                       => $row['id'] ?: null,
                        'appointment_date'         => $row['appointment_date'] ?: null,
                        'appointment_hour'         => $row['appointment_hour'] ?: null,
                        'ailments'                 => $row['ailments'] ?: null,
                        'diagnosis'                => $row['diagnosis'] ?: null,
                        'surgeries'                => $row['surgeries'] ?: null,
                        'reflexology_diagnostics'  => $row['reflexology_diagnostics'] ?: null,
                        'medications'              => $row['medications'] ?: null,
                        'observation'              => $row['observation'] ?: null,
                        'initial_date'             => $row['initial_date'] ?: null,
                        'final_date'               => $row['final_date'] ?: null,
                        'appointment_type'         => $row['appointment_type'] ?: null,
                        'room'                     => $row['room'] ?: null,
                        'social_benefit'           => $row['social_benefit'] ?: null,
                        'payment'                  => $row['payment'] ?: null,
                        'ticket_number'            => $row['ticket_number'] ?: null,
                        'appointment_status_id'    => $row['appointment_status_id'] ?: null,
                        'payment_type_id'          => $row['payment_type_id'] ?: null,
                        'patient_id'               => $row['patient_id'] ?: null,
                        'therapist_id'             => $row['therapist_id'] ?: null,
                        'created_at'               => $row['created_at'] ?: now(),
                        'updated_at'               => $row['updated_at'] ?: now(),
                        'deleted_at'               => $row['deleted_at'] ?: null,
                    ];
                }

                Appointment::insert($appointments);
            });

            DB::enableQueryLog();

            $this->command->info('✅ Seeding completado con éxito. 🎉');
        } catch (Exception $e) {
            $this->command->error('❌ Error durante el seeding: ' . $e->getMessage());
        }
    }
}
