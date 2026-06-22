<?php

namespace Database\Seeders;


use App\Models\GeneralInstitutionCategory;
use Illuminate\Database\Seeder;

class GeneralInstitutionCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = ['Pemerintah Provinsi', 'Pemerintah Kabupaten/Kota', 'Pemda Lainnya', 'K/L/LAINNYA'];

        foreach ($categories as $cat) {
            GeneralInstitutionCategory::firstOrCreate(['name' => $cat]);
        }
    }
}
