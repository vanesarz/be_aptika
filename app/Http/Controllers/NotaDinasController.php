<?php

namespace App\Http\Controllers;

use App\Models\NotaDinas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NotaDinasController extends Controller
{
    /**
     * INDEX — list dengan search, filter status, dan pagination.
     */
    public function index(Request $request)
    {
        $query = NotaDinas::with('user')->latest();

        // Search: nomor_surat, perihal, tujuan
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                  ->orWhere('perihal', 'like', "%{$search}%")
                  ->orWhere('tujuan', 'like', "%{$search}%");
            });
        }

        // Filter status
        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $perPage = $request->query('per_page', 10);
        $data = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $data->items(),
            'meta' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ],
        ]);
    }

    /**
     * STORE — buat nota dinas baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tujuan' => 'required|string|max:255',
            'dari' => 'required|string|max:255',
            'tembusan' => 'nullable|string|max:255',
            'sifat_surat' => 'required|in:biasa,penting,rahasia',
            'perihal' => 'required|string|max:1000',
            'isi_surat' => 'nullable|string',
            'isi_lampiran' => 'nullable|string',
            'tanggal_surat' => 'required|date',
            'status' => 'nullable|in:draft,terkirim,menunggu_tte',
            'lampiran' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'catatan' => 'nullable|string',
        ]);

        $validated['nomor_surat'] = NotaDinas::generateNomorSurat();
        $validated['user_id'] = $request->user()->id;
        $validated['status'] = $validated['status'] ?? 'draft';

        if ($request->hasFile('lampiran')) {
            $validated['lampiran'] = $request
                ->file('lampiran')
                ->store('nota-dinas', 'public');
        }

        $notaDinas = NotaDinas::create($validated);
        $notaDinas->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Nota Dinas berhasil dibuat.',
            'data' => $notaDinas,
        ], 201);
    }

    /**
     * SHOW — detail nota dinas.
     */
    public function show($id)
    {
        $notaDinas = NotaDinas::with('user')->findOrFail($id);

        $data = $notaDinas->toArray();
        if ($notaDinas->lampiran) {
            $data['lampiran_url'] = asset('storage/' . $notaDinas->lampiran);
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * UPDATE — edit nota dinas.
     */
    public function update(Request $request, $id)
    {
        $notaDinas = NotaDinas::findOrFail($id);

        $validated = $request->validate([
            'tujuan' => 'required|string|max:255',
            'dari' => 'required|string|max:255',
            'tembusan' => 'nullable|string|max:255',
            'sifat_surat' => 'required|in:biasa,penting,rahasia',
            'perihal' => 'required|string|max:1000',
            'isi_surat' => 'nullable|string',
            'isi_lampiran' => 'nullable|string',
            'tanggal_surat' => 'required|date',
            'status' => 'nullable|in:draft,terkirim,menunggu_tte',
            'lampiran' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'catatan' => 'nullable|string',
        ]);

        if ($request->hasFile('lampiran')) {
            // Hapus file lama
            if ($notaDinas->lampiran && Storage::disk('public')->exists($notaDinas->lampiran)) {
                Storage::disk('public')->delete($notaDinas->lampiran);
            }

            $validated['lampiran'] = $request
                ->file('lampiran')
                ->store('nota-dinas', 'public');
        }

        $notaDinas->update($validated);
        $notaDinas->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Nota Dinas berhasil diupdate.',
            'data' => $notaDinas,
        ]);
    }

    /**
     * DELETE — hapus nota dinas.
     */
    public function destroy($id)
    {
        $notaDinas = NotaDinas::findOrFail($id);

        // Hapus file lampiran jika ada
        if ($notaDinas->lampiran && Storage::disk('public')->exists($notaDinas->lampiran)) {
            Storage::disk('public')->delete($notaDinas->lampiran);
        }

        $notaDinas->delete();

        return response()->json([
            'success' => true,
            'message' => 'Nota Dinas berhasil dihapus.',
        ]);
    }

    /**
     * EXPORT — download data sebagai CSV.
     */
    public function export(Request $request)
    {
        $query = NotaDinas::with('user')->latest();

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $data = $query->get();

        $csvHeader = "No,Nomor Surat,Tujuan,Dari,Tembusan,Sifat,Perihal,Tanggal Surat,Status,Pembuat,Catatan\n";
        $csvBody = '';

        foreach ($data as $index => $item) {
            $statusLabel = match ($item->status) {
                'draft' => 'Draft',
                'terkirim' => 'Terkirim',
                'menunggu_tte' => 'Menunggu TTE',
                default => $item->status,
            };

            $csvBody .= implode(',', [
                $index + 1,
                '"' . str_replace('"', '""', $item->nomor_surat) . '"',
                '"' . str_replace('"', '""', $item->tujuan) . '"',
                '"' . str_replace('"', '""', $item->dari ?? '') . '"',
                '"' . str_replace('"', '""', $item->tembusan ?? '') . '"',
                ucfirst($item->sifat_surat),
                '"' . str_replace('"', '""', $item->perihal) . '"',
                $item->tanggal_surat ? $item->tanggal_surat->format('Y-m-d') : '',
                $statusLabel,
                '"' . str_replace('"', '""', $item->user->name ?? '') . '"',
                '"' . str_replace('"', '""', $item->catatan ?? '') . '"',
            ]) . "\n";
        }

        $filename = 'nota_dinas_' . now()->format('Ymd_His') . '.csv';

        return response($csvHeader . $csvBody, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
