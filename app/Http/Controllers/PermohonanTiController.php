<?php

namespace App\Http\Controllers;

use App\Models\PermohonanTi;
use App\Models\GeneralOpd;
use Illuminate\Http\Request;

class PermohonanTiController extends Controller
{
    public function index(Request $request)
    {
        $query = PermohonanTi::latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('no_rfc', 'like', "%{$search}%")
                  ->orWhere('pemohon', 'like', "%{$search}%")
                  ->orWhere('nama_perangkat_daerah', 'like', "%{$search}%")
                  ->orWhere('nama_aplikasi', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $data = $query->get();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function getOpd()
    {
        $opds = GeneralOpd::select('id', 'name')->get();
        if ($opds->isEmpty()) {
            $defaultOpds = [
                ['id' => 1, 'name' => 'BADAN PENGHUBUNG'],
                ['id' => 2, 'name' => 'SEKRETARIAT DPRD'],
                ['id' => 3, 'name' => 'DINAS KEPENDUDUKAN DAN PENCATATAN SIPIL'],
                ['id' => 14, 'name' => 'DINAS KOMUNIKASI DAN INFORMATIKA'],
                ['id' => 36, 'name' => 'SEKRETARIAT DAERAH'],
            ];
            return response()->json($defaultOpds);
        }
        return response()->json($opds);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pemohon' => 'required|string',
            'unit_kerja' => 'nullable|string',
            'perangkat_daerah_id' => 'nullable|integer',
            'nama_perangkat_daerah' => 'nullable|string',
            'nomor_kontak' => 'nullable|string',
            'email_dinas' => 'nullable|string',
            'jenis_perubahan' => 'nullable',
            'jenis_permohonan' => 'nullable',
            'nama_aplikasi' => 'nullable|string',
            'deskripsi_aplikasi' => 'nullable|string',
            'alamat_aplikasi' => 'nullable|string',
            'alamat_repository' => 'nullable|string',
            'latar_belakang' => 'nullable|string',
            'rincian_perubahan' => 'nullable|string',
            'risiko_tidak_dilakukan' => 'nullable|string',
            'kriteria_risiko' => 'nullable',
            'keterangan' => 'nullable|string',
            'solusi_diharapkan' => 'nullable|string',
            'risiko_perubahan' => 'nullable|string',
            'alternatif_perubahan' => 'nullable|string',
            'biaya_perubahan' => 'nullable|string',
            'waktu_perubahan' => 'nullable|string',
        ]);

        $validated['no_rfc'] = PermohonanTi::generateNoRfc();
        $validated['tanggal_permohonan'] = now()->toDateString();
        $validated['status'] = 'Menunggu';

        if (is_string($request->input('jenis_perubahan'))) {
            $decoded = json_decode($request->input('jenis_perubahan'), true);
            $validated['jenis_perubahan'] = is_array($decoded) ? $decoded : [$request->input('jenis_perubahan')];
        }
        if (is_string($request->input('jenis_permohonan'))) {
            $decoded = json_decode($request->input('jenis_permohonan'), true);
            $validated['jenis_permohonan'] = is_array($decoded) ? $decoded : [$request->input('jenis_permohonan')];
        }
        if (is_string($request->input('kriteria_risiko'))) {
            $decoded = json_decode($request->input('kriteria_risiko'), true);
            $validated['kriteria_risiko'] = is_array($decoded) ? $decoded : [$request->input('kriteria_risiko')];
        }

        if ($request->hasFile('tanda_tangan_file')) {
            $path = $request->file('tanda_tangan_file')->store('tanda_tangan', 'public');
            $validated['tanda_tangan_file'] = $path;
        }

        if ($request->hasFile('dokumen_pendukung_file')) {
            $paths = [];
            $files = $request->file('dokumen_pendukung_file');
            if (is_array($files)) {
                foreach ($files as $file) {
                    $paths[] = $file->store('dokumen_pendukung', 'public');
                }
            } else {
                $paths[] = $files->store('dokumen_pendukung', 'public');
            }
            $validated['dokumen_pendukung_file'] = $paths;
        }

        $item = PermohonanTi::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Permohonan Perubahan TI berhasil dikirimkan',
            'data' => $item,
        ], 201);
    }

    public function getByRfc($rfc)
    {
        $item = PermohonanTi::where('no_rfc', $rfc)->firstOrFail();
        return response()->json([
            'success' => true,
            'data' => $item,
        ]);
    }

    public function show($id)
    {
        $item = PermohonanTi::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $item,
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $item = PermohonanTi::findOrFail($id);
        $validated = $request->validate([
            'status' => 'required|string',
        ]);

        $item->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => 'Status permohonan berhasil diperbarui',
            'data' => $item,
        ]);
    }

    public function assign(Request $request, $id)
    {
        $item = PermohonanTi::findOrFail($id);
        $validated = $request->validate([
            'assigned_to' => 'nullable|string',
        ]);

        $item->update(['assigned_to' => $validated['assigned_to']]);

        return response()->json([
            'success' => true,
            'message' => 'Penugasan permohonan berhasil diperbarui',
            'data' => $item,
        ]);
    }
}
