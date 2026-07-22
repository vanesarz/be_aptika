<?php

namespace App\Http\Controllers;

use App\Models\NotaDinas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NotaDinasController extends Controller
{
    /**
     * Display a listing of Nota Dinas.
     */
    public function index(Request $request)
    {
        $query = NotaDinas::with('user:id,name,email')->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                  ->orWhere('perihal', 'like', "%{$search}%")
                  ->orWhere('tujuan', 'like', "%{$search}%")
                  ->orWhere('dari', 'like', "%{$search}%")
                  ->orWhere('isi_surat', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $perPage = (int) $request->input('per_page', 10);
        $paginated = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $paginated->items(),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }

    /**
     * Store a newly created Nota Dinas in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tujuan' => 'required|string',
            'dari' => 'nullable|string',
            'tembusan' => 'nullable|string',
            'sifat_surat' => 'nullable|in:biasa,penting,rahasia',
            'perihal' => 'required|string',
            'isi_surat' => 'nullable|string',
            'isi_lampiran' => 'nullable|string',
            'tanggal_surat' => 'required|date',
            'status' => 'nullable|string',
            'catatan' => 'nullable|string',
            'lampiran' => 'nullable|file|max:10240', // Max 10MB
        ]);

        $validated['nomor_surat'] = NotaDinas::generateNomorSurat();
        $validated['user_id'] = auth()->id() ?? 1;
        $validated['dari'] = $validated['dari'] ?? 'Biro Umum dan Administrasi';
        $validated['sifat_surat'] = $validated['sifat_surat'] ?? 'biasa';
        $validated['status'] = $validated['status'] ?? 'draft';

        if ($request->hasFile('lampiran')) {
            $path = $request->file('lampiran')->store('nota_dinas_lampiran', 'public');
            $validated['lampiran'] = $path;
        }

        $notaDinas = NotaDinas::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Nota Dinas berhasil dibuat',
            'data' => $notaDinas->load('user:id,name,email'),
        ], 201);
    }

    /**
     * Display the specified Nota Dinas.
     */
    public function show($id)
    {
        $notaDinas = NotaDinas::with('user:id,name,email')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $notaDinas,
        ]);
    }

    /**
     * Update the specified Nota Dinas in storage.
     */
    public function update(Request $request, $id)
    {
        $notaDinas = NotaDinas::findOrFail($id);

        $validated = $request->validate([
            'tujuan' => 'sometimes|required|string',
            'dari' => 'nullable|string',
            'tembusan' => 'nullable|string',
            'sifat_surat' => 'nullable|in:biasa,penting,rahasia',
            'perihal' => 'sometimes|required|string',
            'isi_surat' => 'nullable|string',
            'isi_lampiran' => 'nullable|string',
            'tanggal_surat' => 'sometimes|required|date',
            'status' => 'nullable|string',
            'catatan' => 'nullable|string',
            'lampiran' => 'nullable|file|max:10240',
        ]);

        if ($request->hasFile('lampiran')) {
            if ($notaDinas->lampiran && Storage::disk('public')->exists($notaDinas->lampiran)) {
                Storage::disk('public')->delete($notaDinas->lampiran);
            }
            $path = $request->file('lampiran')->store('nota_dinas_lampiran', 'public');
            $validated['lampiran'] = $path;
        }

        $notaDinas->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Nota Dinas berhasil diperbarui',
            'data' => $notaDinas->load('user:id,name,email'),
        ]);
    }

    /**
     * Remove the specified Nota Dinas from storage.
     */
    public function destroy($id)
    {
        $notaDinas = NotaDinas::findOrFail($id);

        if ($notaDinas->lampiran && Storage::disk('public')->exists($notaDinas->lampiran)) {
            Storage::disk('public')->delete($notaDinas->lampiran);
        }

        $notaDinas->delete();

        return response()->json([
            'success' => true,
            'message' => 'Nota Dinas berhasil dihapus',
        ]);
    }

    /**
     * Export Nota Dinas data as CSV file.
     */
    public function export(Request $request)
    {
        $query = NotaDinas::latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $items = $query->get();

        $filename = 'nota_dinas_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($items) {
            $file = fopen('php://output', 'w');
            // Write BOM for Excel UTF-8 support
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'ID',
                'Nomor Surat',
                'Tanggal Surat',
                'Dari',
                'Tujuan',
                'Sifat Surat',
                'Perihal',
                'Status',
                'Dibuat Pada'
            ]);

            foreach ($items as $item) {
                fputcsv($file, [
                    $item->id,
                    $item->nomor_surat,
                    $item->tanggal_surat ? $item->tanggal_surat->format('Y-m-d') : '-',
                    $item->dari,
                    $item->tujuan,
                    $item->sifat_surat,
                    $item->perihal,
                    $item->status,
                    $item->created_at ? $item->created_at->format('Y-m-d H:i:s') : '-'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

