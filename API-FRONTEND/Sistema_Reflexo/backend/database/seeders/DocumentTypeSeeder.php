<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $documentTypes = [
            [
                'name' => 'DNI',
                'description' => 'Documento Nacional de Identidad'
            ],
            [
                'name' => 'Carné de Extranjería',
                'description' => 'Identificación para extranjeros residentes en Perú.'
            ],
            [
                'name' => 'Pasaporte',
                'description' => 'Documento de identificación internacional.'
            ],
            [
                'name' => 'Carné de Refugiado',
                'description' => 'Documento para solicitantes de refugio en Perú.'
            ],
            [
                'name' => 'PTP',
                'description' => 'Permiso Temporal de Permanencia (Perú).'
            ],
        ];

        foreach ($documentTypes as $type) {
            DocumentType::firstOrCreate(['name' => $type['name']], $type);
        }
    }
}
