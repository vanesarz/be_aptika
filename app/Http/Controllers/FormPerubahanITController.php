<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\GeneralOpd;

class FormPerubahanITController extends Controller
{
    // ==========================================
    // 1. READ: Ambil Daftar OPD untuk Dropdown
    // ==========================================
    public function getOpdList()
    {
        $opd = GeneralOpd::select('id', 'name')->orderBy('name', 'asc')->get();
        return response()->json($opd);
    }

    // ==========================================
    // 2. READ: Ambil Semua Data Pengajuan (List)
    // ==========================================
    public function index()
    {
        try {
            // Mengambil semua data pengajuan dengan join ke tabel general_opd untuk mendapatkan nama instansi
            $lists = DB::table('form_perubahan_it')
                ->leftJoin('general_opd', 'form_perubahan_it.perangkat_daerah_id', '=', 'general_opd.id')
                ->select('form_perubahan_it.*', 'general_opd.name as nama_perangkat_daerah')
                ->orderBy('form_perubahan_it.id', 'desc')
                ->get();

            // Lakukan decode field JSON agar Frontend menerima format Array, bukan String mentah
            foreach ($lists as $item) {
                $item->jenis_perubahan = json_decode($item->jenis_perubahan ?? '[]');
                $item->jenis_permohonan = json_decode($item->jenis_permohonan ?? '[]');
                $item->kriteria_risiko = json_decode($item->kriteria_risiko ?? '[]');
            }

            return response()->json($lists, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mengambil data: ' . $e->getMessage()], 500);
        }
    }

    // ==========================================
    // 3. CREATE: Simpan Data Pengajuan Baru
    // ==========================================
    public function store(Request $request)
    {
        $request->validate([
            'pemohon' => 'required|string',
            'perangkat_daerah_id' => 'required|exists:general_opd,id',
            'nomor_kontak' => 'required|string',
            'tanggal_permohonan' => 'required|date',
        ]);

        try {
            $noRfc = DB::transaction(function () {
                $lastRecord = DB::table('form_perubahan_it')->orderBy('id', 'desc')->first();
                $nextId = $lastRecord ? $lastRecord->id + 1 : 1;
                return 'RFC-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
            });

            $id = DB::table('form_perubahan_it')->insertGetId([
                'no_rfc' => $noRfc,
                'pemohon' => $request->pemohon,
                'unit_kerja' => $request->unit_kerja,
                'perangkat_daerah_id' => $request->perangkat_daerah_id,
                'nomor_kontak' => $request->nomor_kontak,
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
            return response()->json(['error' => 'Gagal menyimpan data: ' . $e->getMessage()], 500);
        }
    }

    // ==========================================
    // 4. READ: Ambil Detail Berdasarkan ID
    // ==========================================
    public function show($id)
    {
        try {
            $data = DB::table('form_perubahan_it')->where('id', $id)->first();

            if (!$data) {
                return response()->json(['message' => 'Data tidak ditemukan.'], 404);
            }

            // Decode data JSON
            $data->jenis_perubahan = json_decode($data->jenis_perubahan ?? '[]');
            $data->jenis_permohonan = json_decode($data->jenis_permohonan ?? '[]');
            $data->kriteria_risiko = json_decode($data->kriteria_risiko ?? '[]');

            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mengambil detail data: ' . $e->getMessage()], 500);
        }
    }

    // ==========================================
    // 5. UPDATE: Perbarui Data Pengajuan
    // ==========================================
    public function update(Request $request, $id)
    {
        $request->validate([
            'pemohon' => 'required|string',
            'perangkat_daerah_id' => 'required|exists:general_opd,id',
            'nomor_kontak' => 'required|string',
            'tanggal_permohonan' => 'required|date',
        ]);

        try {
            $exists = DB::table('form_perubahan_it')->where('id', $id)->exists();

            if (!$exists) {
                return response()->json(['message' => 'Data tidak ditemukan.'], 404);
            }

            DB::table('form_perubahan_it')->where('id', $id)->update([
                'pemohon' => $request->pemohon,
                'unit_kerja' => $request->unit_kerja,
                'perangkat_daerah_id' => $request->perangkat_daerah_id,
                'nomor_kontak' => $request->nomor_kontak,
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
                'updated_at' => now(),
            ]);

            return response()->json(['message' => 'Data formulir berhasil diperbarui!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memperbarui data: ' . $e->getMessage()], 500);
        }
    }

    // ==========================================
    // 6. DELETE: Hapus Data Pengajuan
    // ==========================================
    public function destroy($id)
    {
        try {
            $data = DB::table('form_perubahan_it')->where('id', $id);

            if (!$data->exists()) {
                return response()->json(['message' => 'Data tidak ditemukan.'], 404);
            }

            $data->delete();

            return response()->json(['message' => 'Data formulir berhasil dihapus!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menghapus data: ' . $e->getMessage()], 500);
        }
    }
}