<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Usuario ADMIN
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'document_type_id' => 1,
                'document_number' => '12345678',
                'photo_url' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQAanNRYrgf5lJfwDZ4Hir2c9VnF1wDxi8yeA&s',
                'name' => 'Admin',
                'sex' => 'M',
                'paternal_lastname' => 'Principal',
                'maternal_lastname' => 'Gonzales',
                'phone' => '999999999',
                'user_name' => 'admin',
                'password' => Hash::make('password123'),
                'country_id' => 1,
            ]
        );

        if (!$admin->hasRole(RoleEnum::ADMIN->value)) {
            $admin->assignRole(RoleEnum::ADMIN->value);
        }

        // Usuario MEMBER
        $member = User::firstOrCreate(
            ['email' => 'member@example.com'],
            [
                'document_type_id' => 1,
                'document_number' => '87654321',
                'photo_url' => 'https://via.placeholder.com/150',
                'name' => 'Miembro',
                'sex' => 'F',
                'paternal_lastname' => 'Secundario',
                'maternal_lastname' => 'Ramírez',
                'phone' => '988888888',
                'user_name' => 'member',
                'password' => Hash::make('password123'),
                'country_id' => 1,
            ]
        );

        if (!$member->hasRole(RoleEnum::MEMBER->value)) {
            $member->assignRole(RoleEnum::MEMBER->value);
        }
    }
}