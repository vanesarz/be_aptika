<?php

namespace Database\Seeders;

use App\Models\Rekening;
use Illuminate\Database\Seeder;

class RekeningSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            [
                'kode_rekening' => '5.1.02.04.01.0001',
                'nomor_rekening' => '524111',
                'nama_rekening' => 'Belanja Perjalanan Dinas Dalam Daerah',
            ],
            [
                'kode_rekening' => '5.1.02.04.01.0002',
                'nomor_rekening' => '524112',
                'nama_rekening' => 'Belanja Perjalanan Dinas Luar Daerah',
            ],
            [
                'kode_rekening' => '5.1.02.04.01.0003',
                'nomor_rekening' => '524113',
                'nama_rekening' => 'Belanja Perjalanan Dinas Biasa',
            ],
        ];

        foreach ($accounts as $acc) {
            Rekening::updateOrCreate(
                ['kode_rekening' => $acc['kode_rekening']],
                $acc
            );
        }
    }
}
