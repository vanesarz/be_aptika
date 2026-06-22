<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RegencyName;


class RegencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $regency = [
            "Kabupaten Bandung",
            "Kabupaten Bandung Barat",
            "Kabupaten Bekasi",
            "Kabupaten Bogor",
            "Kabupaten Ciamis",
            "Kabupaten Cianjur",
            "Kabupaten Cirebon",
            "Kabupaten Garut",
            "Kabupaten Indramayu",
            "Kabupaten Karawang",
            "Kabupaten Kuningan",
            "Kabupaten Majalengka",
            "Kabupaten Pangandaran",
            "Kabupaten Purwakarta",
            "Kabupaten Subang",
            "Kabupaten Sukabumi",
            "Kabupaten Sumedang",
            "Kabupaten Tasikmalaya",
            "Kota Bandung",
            "Kota Banjar",
            "Kota Bekasi",
            "Kota Bogor",
            "Kota Cimahi",
            "Kota Cirebon",
            "Kota Depok",
            "Kota Sukabumi",
            "Kota Tasikmalaya"
        ];

        foreach ($regency as $reg) {
            RegencyName::firstOrCreate(['name' => $reg]);
        }
    }
}
