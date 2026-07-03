<?php

namespace Database\Seeders;

use App\Models\Pegawai;
use Illuminate\Database\Seeder;

class PegawaiSeeder extends Seeder
{
    public function run(): void
    {
        $pegawai = [
            [
                'nama' => 'Ahmad Fauzi',
                'nip' => '198712122010011001',
                'tanggal_lahir' => '1987-12-12',
                'pangkat' => 'Pembina / IVa',
                'jabatan' => 'Kepala Bidang Aplikasi dan Informatika',
                'role' => 'kabid',
            ],
            [
                'nama' => 'Budi Santoso',
                'nip' => '199301012020121001',
                'tanggal_lahir' => '1993-01-01',
                'pangkat' => 'Penata / IIIc',
                'jabatan' => 'Pranata Komputer Ahli Muda',
                'role' => 'staff',
            ],
            [
                'nama' => 'Irna Lestari',
                'nip' => '199505152021122002',
                'tanggal_lahir' => '1995-05-15',
                'pangkat' => 'Penata Muda / IIIa',
                'jabatan' => 'Front Desk Attendant',
                'role' => 'staff',
            ],
            [
                'nama' => 'Rian Hidayat',
                'nip' => '199408242022031003',
                'tanggal_lahir' => '1994-08-24',
                'pangkat' => 'Penata Muda / IIIa',
                'jabatan' => 'Analisis Sistem SPBE',
                'role' => 'staff',
            ],
        ];

        foreach ($pegawai as $p) {
            Pegawai::updateOrCreate(
                ['nip' => $p['nip']],
                $p
            );
        }
    }
}
