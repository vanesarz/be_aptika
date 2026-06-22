<?php

namespace Database\Seeders;


use App\Models\ServiceType;
use Illuminate\Database\Seeder;

class ServiceTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = ['SADA Jabar', 'Smart Jabar', 'Rekayasa Applikasi', 'Integrasi-Interop', 'Pengelolaan Aplikasi', 'Sidebar'];

        foreach ($types as $type) {
            ServiceType::firstOrCreate(['name' => $type]);
        }
    }
}
