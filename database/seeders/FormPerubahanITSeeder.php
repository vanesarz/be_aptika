<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FormPerubahanITSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan ada data di tabel general_opd terlebih dahulu untuk menghindari relasi foreign key error.
        // Di sini kita berasumsi ID 1 dan ID 2 tersedia di tabel general_opd.
        $opdId1 = DB::table('general_opd')->first()->id ?? 1;
        $opdId2 = DB::table('general_opd')->skip(1)->first()->id ?? 2;

        DB::table('form_perubahan_it')->insert([
            [
                'no_rfc' => 'RFC-001',
                'status' => 'disetujui',
                'pemohon' => 'Ahmad Subagja, S.Kom.',
                'unit_kerja' => 'Bidang Aplikasi Informatika',
                'perangkat_daerah_id' => $opdId1,
                'nomor_kontak' => '081234567890',
                'email_dinas' => 'ahmad.subagja@jabarprov.go.id',
                
                // Format data JSON array checkbox
                'jenis_perubahan' => json_encode(['Aplikasi', 'Infrastruktur']),
                'jenis_permohonan' => json_encode(['Perubahan Mayor']),
                
                'nama_aplikasi' => 'Sistem Informasi Layanan Publik Jabar',
                'deskripsi_aplikasi' => 'Aplikasi internal untuk mengelola aduan dan aspirasi masyarakat Jawa Barat.',
                'alamat_aplikasi' => 'https://layanan.jabarprov.go.id',
                'alamat_repository' => 'https://github.com/diskominfojabar/layanan-publik',
                
                'latar_belakang' => 'Migrasi server ke Cloud Hosting baru dan pembaharuan modul keamanan API.',
                'rincian_perubahan' => 'Pembaruan framework Next.js ke versi terbaru, konfigurasi SSL, serta migrasi database PostgreSQL.',
                'risiko_tidak_dilakukan' => 'Sistem rentan terhadap celah keamanan (exploit) lama dan performa melambat saat trafik tinggi.',
                
                'kriteria_risiko' => json_encode(['Agak Berat']),
                'keterangan' => 'Diharapkan migrasi dilakukan pada malam hari jam low-traffic (00:00 - 04:00 WIB).',
                'solusi_diharapkan' => 'Sistem berjalan lancar di cluster cloud baru tanpa adanya downtime berkepanjangan.',
                'risiko_perubahan' => 'Potensi kegagalan koneksi database sesaat saat perpindahan DNS.',
                'alternatif_perubahan' => 'Rollback ke server lama menggunakan snapshot terakhir jika downtime melebihi 30 menit.',
                
                'biaya_perubahan' => '0', // Menggunakan default string 0 karena difasilitasi internal
                'waktu_perubahan' => '4 Jam',
                'lampiran' => null,
                'tanggal_permohonan' => Carbon::now()->subDays(5)->format('Y-m-d'),
                
                // Mengisi path dummy file hasil upload
                'tanda_tangan_file' => 'tanda_tangan/dummy_ttd_1.png',
                'dokumen_pendukung_file' => json_encode([
                    'dokumen_pendukung/dummy_doc_topology.pdf',
                    'dokumen_pendukung/dummy_doc_risk_analysis.png'
                ]),
                
                'setuju_data_benar' => 1,
                'setuju_atasan' => 1,
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(5),
            ],
            [
                'no_rfc' => 'RFC-002',
                'status' => 'menunggu',
                'pemohon' => 'Siti Aminah, M.T.',
                'unit_kerja' => 'Seksi Data dan Statistik',
                'perangkat_daerah_id' => $opdId2,
                'nomor_kontak' => '085711223344',
                'email_dinas' => 'siti.aminah@jabarprov.go.id',
                
                'jenis_perubahan' => json_encode(['Database']),
                'jenis_permohonan' => json_encode(['Perubahan Minor']),
                
                'nama_aplikasi' => 'Open Data Provinsi Jabar',
                'deskripsi_aplikasi' => 'Portal rujukan satu data untuk pemenuhan kebutuhan data publik masyarakat.',
                'alamat_aplikasi' => 'https://data.jabarprov.go.id',
                'alamat_repository' => 'https://github.com/diskominfojabar/open-data',
                
                'latar_belakang' => 'Optimasi struktur query table log dan indeks data spasial sektoral.',
                'rincian_perubahan' => 'Penambahan indexing pada table `transaksi_data` dan pembersihan data log usang di atas 2 tahun.',
                'risiko_tidak_dilakukan' => 'Proses memuat visualisasi grafik data di dashboard utama memakan waktu terlalu lama (timeout).',
                
                'kriteria_risiko' => json_encode(['Tidak Berat']),
                'keterangan' => 'Proses eksekusi script SQL diupayakan tidak mengunci (lock) tabel utama pendaftaran.',
                'solusi_diharapkan' => 'Kecepatan load dataset meningkat minimal 50% lebih cepat.',
                'risiko_perubahan' => 'Beban CPU server database naik sesaat selama proses pembuatan index baru.',
                'alternatif_perubahan' => 'Pembatalan proses index (Drop Index) apabila memicu penumpukan antrean koneksi pool.',
                
                'biaya_perubahan' => '0',
                'waktu_perubahan' => '30 Menit',
                'lampiran' => null,
                'tanggal_permohonan' => Carbon::now()->format('Y-m-d'),
                
                'tanda_tangan_file' => 'tanda_tangan/dummy_ttd_1.png',
                // Contoh jika dokumen pendukung dikosongkan (array kosong dalam format JSON)
                'dokumen_pendukung_file' => json_encode([]), 
                
                'setuju_data_benar' => 1,
                'setuju_atasan' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);
    }
}