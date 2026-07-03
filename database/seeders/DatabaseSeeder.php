<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ServiceTypeSeeder::class,
            GeneralOpdSeeder::class,
            GeneralInstitutionCategorySeeder::class,
            DocumentTypeSeeder::class,
            RegencySeeder::class,
            RekeningSeeder::class,
            PegawaiSeeder::class,
        ]);

        // Seed Admin User
        User::updateOrCreate(
            ['email' => 'admin@aptika.com'],
            [
                'name' => 'Admin Aptika',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'is_active' => 1,
            ]
        );

        // Seed Regular User
        User::updateOrCreate(
            ['email' => 'user@aptika.com'],
            [
                'name' => 'User Aptika',
                'password' => bcrypt('password'),
                'role' => 'user',
                'is_active' => 1,
            ]
        );
    }
}
