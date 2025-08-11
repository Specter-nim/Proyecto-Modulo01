<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            DocumentTypeSeeder::class,
            PaymentTypeSeeder::class,
            AppointmentStatusSeeder::class,
            CountrySeeder::class,
            RegionSeeder::class,
            ProvinceSeeder::class,
            DistrictSeeder::class,
            TherapistSeeder::class,
            PatientSeeder::class,
            HistorySeeder::class,
            PredeterminedPriceSeeder::class,
            AppointmentSeeder::class,
            RoleSeeder::class,
            PermissionSeeder::class,
            UserSeeder::class,
        ]);
    }
}
