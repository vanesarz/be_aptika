<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\GeneralOpd;

class FormPerubahanITController extends Controller
{
    // API untuk mengambil daftar OPD untuk Dropdown
    public function getOpdList()
    {
        // Mengambil dari model GeneralOpd (sesuai seeder database)
        $opd = GeneralOpd::select('id', 'name')->orderBy('name', 'asc')->get();
        return response()->json($opd);
    }

    // API untuk menyimpan data form
    public function store(Request $request)
    {
        // Validasi input wajib sesuai kebutuhan form database
        $request->validate([
            'pemohon' => 'required|string',
            'perangkat_daerah_id' => 'required|exists:general_opd,id',
            'nomor_kontak' => 'required|string',
            'tanggal_permohonan' => 'required|date',
        ]);

        try {
            // Gunakan Database Transaction agar aman jika diakses secara bersamaan
            $noRfc = DB::transaction(function () {
                // 1. Ambil record terakhir yang ada di tabel form_perubahan_it
                $lastRecord = DB::table('form_perubahan_it')->orderBy('id', 'desc')->first();
                $nextId = $lastRecord ? $lastRecord->id + 1 : 1;

                // 2. Format menjadi RFC-xxx (contoh: ID 1 menjadi RFC-001, ID 18 menjadi RFC-018)
                return 'RFC-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
            });

            // 3. Simpan ke database bersama seluruh properti form dari Next.js
            $id = DB::table('form_perubahan_it')->insertGetId([
                'no_rfc' => $noRfc,
                'pemohon' => $request->pemohon,
                'unit_kerja' => $request->unit_kerja,
                'perangkat_daerah_id' => $request->perangkat_daerah_id,
                'nomor_kontak' => $request->nomor_kontak,
                // Menggunakan null coalescing ?? [] untuk memastikan data checkbox tersimpan dengan aman
                'jenis_perubahan' => json_encode($request->jenis_perubahan ?? []),
                'jenis_permohonan' => json_encode($request->jenis_permohonan ?? []),
                'nama_aplikasi' => $request->nama_aplikasi,
                'deskripsi_aplikasi' => $request->deskripsi_aplikasi,
                'alamat_aplikasi' => $request->alamat_aplikasi,
                'alamat_repository' => $request->alamat_repository,
                'latar_belakang' => $request->latar_belakang,
                'rincian_perubahan' => $request->rincian_perubahan,
                'risiko_tidak_dilakukan' => $request->risiko_tidak_dilakukan,
                'kriteria_risiko' => json_encode($request->kriteria_risiko ?? []),
                'keterangan' => $request->keterangan,
                'solusi_diharapkan' => $request->solusi_diharapkan,
                'risiko_perubahan' => $request->risiko_perubahan,
                'alternatif_perubahan' => $request->alternatif_perubahan,
                'biaya_perubahan' => $request->biaya_perubahan ?? '0',
                'waktu_perubahan' => $request->waktu_perubahan,
                'tanggal_permohonan' => $request->tanggal_permohonan,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'message' => 'Formulir berhasil disubmit!',
                'id' => $id,
                'no_rfc' => $noRfc
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }
}