<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\GeneralOpd;
use Barryvdh\DomPDF\Facade\Pdf;

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

            // Lakukan decode field JSON agar Frontend menerima format Array murni
            foreach ($lists as $item) {
                $item->jenis_perubahan = json_decode($item->jenis_perubahan ?? '[]');
                $item->jenis_permohonan = json_decode($item->jenis_permohonan ?? '[]');
                $item->kriteria_risiko = json_decode($item->kriteria_risiko ?? '[]');
                
                // Decode dokumen pendukung menjadi array url publik
                $dokumenFiles = json_decode($item->dokumen_pendukung_file ?? '[]');
                $item->dokumen_pendukung_urls = array_map(function($path) {
                    return asset('storage/' . $path);
                }, $dokumenFiles);
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
        // Validasi input text, multi-file attachment opsional, pakta integritas, serta kriteria risiko
        $request->validate([
            'pemohon' => 'required|string',
            'email_dinas' => 'required|email:rfc,dns', // Memastikan format email dinas valid
            'perangkat_daerah_id' => 'required|exists:general_opd,id',
            'nomor_kontak' => 'required|string',
            'tanggal_permohonan' => 'required|date',
            'tanda_tangan_file' => 'nullable|image|mimes:jpeg,jpg,png|max:10240', // Maksimal 10MB
            
            // Konfigurasi Validasi Multi-File Dokumen Pendukung (Maksimal 5 Berkas, Boleh Kosong)
            'dokumen_pendukung_file' => 'nullable|array|max:5', 
            'dokumen_pendukung_file.*' => 'file|mimes:jpeg,jpg,png,pdf|max:10240', // Validasi tiap komponen berkas
            
            // Validasi kriteria_risiko dikunci ketat oleh Backend
            'kriteria_risiko' => 'nullable|array',
            'kriteria_risiko.*' => 'in:Malapetaka,Sangat Berat,Berat,Agak Berat,Tidak Berat',
            
            // Checkbox pernyataan wajib disetujui
            'setuju_data_benar' => 'required|accepted',
            'setuju_atasan' => 'required|accepted',
        ]);

        try {
            $noRfc = DB::transaction(function () {
                $lastRecord = DB::table('form_perubahan_it')->orderBy('id', 'desc')->first();
                $nextId = $lastRecord ? $lastRecord->id + 1 : 1;
                return 'RFC-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
            });

            // Handling file upload Tanda Tangan (Single File)
            $ttdPath = null;
            if ($request->hasFile('tanda_tangan_file')) {
                $ttdPath = $request->file('tanda_tangan_file')->store('tanda_tangan', 'public');
            }

            // Handling Multi-File Upload Dokumen Pendukung
            $dokumenPaths = [];
            if ($request->hasFile('dokumen_pendukung_file')) {
                foreach ($request->file('dokumen_pendukung_file') as $file) {
                    $path = $file->store('dokumen_pendukung', 'public');
                    $dokumenPaths[] = $path;
                }
            }

            $id = DB::table('form_perubahan_it')->insertGetId([
                'no_rfc' => $noRfc,
                'status' => 'menunggu',
                'pemohon' => $request->pemohon,
                'email_dinas' => $request->email_dinas,
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
                'tanda_tangan_file' => $ttdPath,
                
                // Mengonversi array path menjadi format teks JSON sebelum masuk DB
                'dokumen_pendukung_file' => json_encode($dokumenPaths),
                
                'setuju_data_benar' => 1,
                'setuju_atasan' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Transformasi path ke bentuk URL publik penuh untuk respon data
            $dokumenUrls = array_map(function($path) {
                return asset('storage/' . $path);
            }, $dokumenPaths);

            return response()->json([
                'message' => 'Formulir beserta berkas dan konfirmasi persetujuan berhasil disubmit!',
                'id' => $id,
                'no_rfc' => $noRfc,
                'status' => 'menunggu',
                'tanda_tangan_url' => $ttdPath ? asset('storage/' . $ttdPath) : null,
                'dokumen_pendukung_urls' => $dokumenUrls
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menyimpan data: ' . $e->getMessage()], 500);
        }
    }

    // ==========================================
    // 4. READ: Ambil Detail Berdasarkan ID (Sudah Di-JOIN)
    // ==========================================
    public function show($id)
    {
        try {
            // Mengambil detail data dengan leftJoin ke tabel general_opd agar nama instansi terbawa ke frontend
            $data = DB::table('form_perubahan_it')
                ->leftJoin('general_opd', 'form_perubahan_it.perangkat_daerah_id', '=', 'general_opd.id')
                ->select('form_perubahan_it.*', 'general_opd.name as nama_perangkat_daerah')
                ->where('form_perubahan_it.id', $id)
                ->first();

            if (!$data) {
                return response()->json(['message' => 'Data tidak ditemukan.'], 404);
            }

            // Decode data JSON
            $data->jenis_perubahan = json_decode($data->jenis_perubahan ?? '[]');
            $data->jenis_permohonan = json_decode($data->jenis_permohonan ?? '[]');
            $data->kriteria_risiko = json_decode($data->kriteria_risiko ?? '[]');
            
            // Tambahkan URL akses penuh berkas tunggal maupun jamak untuk Frontend
            $data->tanda_tangan_url = $data->tanda_tangan_file ? asset('storage/' . $data->tanda_tangan_file) : null;
            
            $dokumenFiles = json_decode($data->dokumen_pendukung_file ?? '[]');
            $data->dokumen_pendukung_urls = array_map(function($path) {
                return asset('storage/' . $path);
            }, $dokumenFiles);

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
            'email_dinas' => 'required|email:rfc,dns',
            'perangkat_daerah_id' => 'required|exists:general_opd,id',
            'nomor_kontak' => 'required|string',
            'tanggal_permohonan' => 'required|date',
            'tanda_tangan_file' => 'nullable|image|mimes:jpeg,jpg,png|max:10240',
            
            // Validasi multi-file pada update data
            'dokumen_pendukung_file' => 'nullable|array|max:5',
            'dokumen_pendukung_file.*' => 'file|mimes:jpeg,jpg,png,pdf|max:10240',
            
            'kriteria_risiko' => 'nullable|array',
            'kriteria_risiko.*' => 'in:Malapetaka,Sangat Berat,Berat,Agak Berat,Tidak Berat',
        ]);

        try {
            $currentData = DB::table('form_perubahan_it')->where('id', $id)->first();

            if (!$currentData) {
                return response()->json(['message' => 'Data tidak ditemukan.'], 404);
            }

            $updateData = [
                'pemohon' => $request->pemohon,
                'email_dinas' => $request->email_dinas,
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
            ];

            // Update Berkas Tanda Tangan
            if ($request->hasFile('tanda_tangan_file')) {
                if ($currentData->tanda_tangan_file) {
                    Storage::disk('public')->delete($currentData->tanda_tangan_file);
                }
                $updateData['tanda_tangan_file'] = $request->file('tanda_tangan_file')->store('tanda_tangan', 'public');
            }

            // Update Banyak Berkas Dokumen Pendukung Sekaligus
            if ($request->hasFile('dokumen_pendukung_file')) {
                // Hapus berkas-berkas dokumen pendukung lama yang tercatat sebelumnya
                $oldFiles = json_decode($currentData->dokumen_pendukung_file ?? '[]');
                foreach ($oldFiles as $oldPath) {
                    if (Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }
                }

                // Masukkan berkas kumpulan dokumen baru
                $newPaths = [];
                foreach ($request->file('dokumen_pendukung_file') as $file) {
                    $newPaths[] = $file->store('dokumen_pendukung', 'public');
                }
                $updateData['dokumen_pendukung_file'] = json_encode($newPaths);
            }

            DB::table('form_perubahan_it')->where('id', $id)->update($updateData);

            return response()->json(['message' => 'Data formulir dan berkas berhasil diperbarui!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memperbarui data: ' . $e->getMessage()], 500);
        }
    }

    // ==========================================
    // 6. UPDATE STATUS: Khusus Pengelolaan Admin
    // ==========================================
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:menunggu,disetujui,ditolak'
        ]);

        try {
            $data = DB::table('form_perubahan_it')->where('id', $id);

            if (!$data->exists()) {
                return response()->json(['message' => 'Data pengajuan tidak ditemukan.'], 404);
            }

            $data->update([
                'status' => $request->status,
                'updated_at' => now()
            ]);

            return response()->json([
                'message' => 'Status formulir berhasil diperbarui menjadi ' . $request->status
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memperbarui status: ' . $e->getMessage()], 500);
        }
    }

    // ==========================================
    // 7. DELETE: Hapus Data Pengajuan beserta Seluruh File Fisik
    // ==========================================
    public function destroy($id)
    {
        try {
            $data = DB::table('form_perubahan_it')->where('id', $id)->first();

            if (!$data) {
                return response()->json(['message' => 'Data tidak ditemukan.'], 404);
            }

            // Hapus file tanda tangan dari storage
            if ($data->tanda_tangan_file) {
                Storage::disk('public')->delete($data->tanda_tangan_file);
            }
            
            // Looping untuk menghapus semua berkas dokumen pendukung jamak dari storage
            $dokumenFiles = json_decode($data->dokumen_pendukung_file ?? '[]');
            foreach ($dokumenFiles as $filePath) {
                if (Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
            }

            DB::table('form_perubahan_it')->where('id', $id)->delete();

            return response()->json(['message' => 'Data formulir beserta seluruh berkas berhasil dihapus!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menghapus data: ' . $e->getMessage()], 500);
        }
    }

    // ==========================================
    // 8. GENERATE PDF: Ekspor ke File PDF Resmi
    // ==========================================
    public function exportPdf($id)
    {
        try {
            $data = DB::table('form_perubahan_it')
                ->leftJoin('general_opd', 'form_perubahan_it.perangkat_daerah_id', '=', 'general_opd.id')
                ->select('form_perubahan_it.*', 'general_opd.name as nama_perangkat_daerah')
                ->where('form_perubahan_it.id', $id)
                ->first();

            if (!$data) {
                return response()->json(['message' => 'Data tidak ditemukan untuk dicetak.'], 404);
            }

            $jenis_perubahan = json_decode($data->jenis_perubahan ?? '[]', true);
            $jenis_permohonan = json_decode($data->jenis_permohonan ?? '[]', true);
            $kriteria_risiko = json_decode($data->kriteria_risiko ?? '[]', true);

            $pdf = Pdf::loadView('pdf.form_perubahan_it', compact(
                'data', 
                'jenis_perubahan', 
                'jenis_permohonan', 
                'kriteria_risiko'
            ));

            $pdf->setPaper('a4', 'portrait');
            return $pdf->stream('Form_Perubahan_IT_' . $data->no_rfc . '.pdf');

        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal membuat file PDF: ' . $e->getMessage()], 500);
        }
    }
}