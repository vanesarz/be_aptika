<?php

namespace App\Http\Controllers;

use App\Models\Magang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MagangController extends Controller
{
    /**
     * INDEX
     */
    public function index()
    {
        $data = Magang::latest()->get()->map(function ($item) {

            return [
                'id' => $item->id,
                'nama' => $item->nama,
                'nama_kampus' => $item->nama_kampus,
                'tgl_mulai' => $item->tgl_mulai_magang,
                'tgl_selesai' => $item->tgl_selesai_magang,
                'status_magang' => $item->status_magang,
                'sertifikat' => $item->sertifikat,
                'cv_magang' => asset('storage/' . $item->cv_magang),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * STORE
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nama_kampus' => 'required|string|max:255',

            'tgl_mulai_magang' => 'required|date',
            'tgl_selesai_magang' => 'required|date',

            'cv_magang' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',

            'sertifikat' => 'nullable|in:Sudah menerima,Belum menerima',

            'keterangan' => 'nullable|string'
        ]);

        if ($request->hasFile('cv_magang')) {
            $validated['cv_magang'] = $request
                ->file('cv_magang')
                ->store('cv-magang', 'public');
        }

        $validated['sertifikat'] = $validated['sertifikat'] ?? 'Belum menerima';

        $magang = Magang::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil ditambahkan.',
            'data' => $magang
        ], 201);
    }

    /**
     * SHOW
     */
    public function show($id)
    {
        $magang = Magang::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                ...$magang->toArray(),
                'cv_magang' => asset('storage/' . $magang->cv_magang)
            ]
        ]);
    }

    /**
     * UPDATE
     */
    public function update(Request $request, $id)
    {
        $magang = Magang::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nama_kampus' => 'required|string|max:255',

            'tgl_mulai_magang' => 'required|date',
            'tgl_selesai_magang' => 'required|date',

            'cv_magang' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',

            'sertifikat' => 'required|in:Sudah menerima,Belum menerima',

            'keterangan' => 'nullable|string'
        ]);

        if ($request->hasFile('cv_magang')) {

            if ($magang->cv_magang && Storage::disk('public')->exists($magang->cv_magang)) {
                Storage::disk('public')->delete($magang->cv_magang);
            }

            $validated['cv_magang'] = $request
                ->file('cv_magang')
                ->store('cv-magang', 'public');
        }

        $magang->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diupdate.',
            'data' => $magang
        ]);
    }

    /**
     * DELETE
     */
    public function destroy($id)
    {
        $magang = Magang::findOrFail($id);

        if ($magang->cv_magang && Storage::disk('public')->exists($magang->cv_magang)) {
            Storage::disk('public')->delete($magang->cv_magang);
        }

        $magang->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil dihapus.'
        ]);
    }
}