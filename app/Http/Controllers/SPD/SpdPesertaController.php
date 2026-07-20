<?php

namespace App\Http\Controllers\SPD;

use App\Http\Controllers\Controller;
use App\Models\DetailPerjalanan;
use App\Models\Pegawai;
use App\Models\SpdPeserta;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SpdPesertaController extends Controller
{
    public function index()
    {
        try {
            $items = SpdPeserta::with(['pegawai', 'perjalanan'])
                ->orderBy('id')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Data peserta SPD berhasil diambil.',
                'data' => $items,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data peserta SPD.',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'detail_perjalanan_id' => 'required|exists:detail_perjalanan,id',
            'pegawai_id' => 'required|array|min:1',
            'pegawai_id.*' => 'required|exists:pegawai,id',
        ]);

        try {
            $items = DB::transaction(function () use ($validated) {
                $detail = DetailPerjalanan::findOrFail($validated['detail_perjalanan_id']);
                $pegawaiIds = array_unique($validated['pegawai_id']);
                $participants = Pegawai::whereIn('id', $pegawaiIds)->get();

                $lamaHari = Carbon::parse($detail->tanggal_berangkat)
                    ->diffInDays(Carbon::parse($detail->tanggal_kembali)) + 1;
                $year = date('Y');
                $existingStaffNomor = SpdPeserta::where('detail_perjalanan_id', $detail->id)
                    ->whereHas('pegawai', function ($query) {
                        $query->where('role', 'staff');
                    })
                    ->value('nomor_spd');

                $staffNomorSpd = $existingStaffNomor ?: $this->buildNomorSpd('ST', $year, $this->getNextNomorSpdSequence('ST', $year));
                $kabidSequence = $this->getNextNomorSpdSequence('KB', $year);

                $created = [];

                foreach ($participants as $participant) {
                    $nomorSpd = $participant->role === 'staff'
                        ? $staffNomorSpd
                        : $this->buildNomorSpd('KB', $year, $kabidSequence++);

                    $created[] = SpdPeserta::create([
                        'detail_perjalanan_id' => $detail->id,
                        'pegawai_id' => $participant->id,
                        'nomor_spd' => $nomorSpd,
                        'lama_hari' => $lamaHari,
                        'total_uang' => $lamaHari * $detail->uang_harian,
                    ]);
                }

                return $created;
            });

            return response()->json([
                'success' => true,
                'message' => 'Peserta SPD berhasil dibuat.',
                'data' => $items,
            ], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Detail perjalanan atau pegawai tidak ditemukan.',
                'errors' => $e->getMessage(),
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat peserta SPD.',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $item = SpdPeserta::with(['pegawai', 'perjalanan'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Data peserta SPD ditemukan.',
                'data' => $item,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Peserta SPD tidak ditemukan.',
                'errors' => $e->getMessage(),
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data peserta SPD.',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'detail_perjalanan_id' => 'required|exists:detail_perjalanan,id',
            'pegawai_id' => 'required|exists:pegawai,id',
        ]);

        try {
            $item = SpdPeserta::findOrFail($id);
            $detail = DetailPerjalanan::findOrFail($validated['detail_perjalanan_id']);
            $participant = Pegawai::findOrFail($validated['pegawai_id']);

            $lamaHari = Carbon::parse($detail->tanggal_berangkat)
                ->diffInDays(Carbon::parse($detail->tanggal_kembali)) + 1;
            $year = date('Y');
            $nomorSpd = $item->nomor_spd;

            if ($participant->role === 'staff') {
                $existingStaffNumber = SpdPeserta::where('detail_perjalanan_id', $detail->id)
                    ->whereHas('pegawai', function ($query) {
                        $query->where('role', 'staff');
                    })
                    ->where('id', '!=', $item->id)
                    ->value('nomor_spd');

                $nomorSpd = $existingStaffNumber ?: $this->buildNomorSpd('ST', $year, $this->getNextNomorSpdSequence('ST', $year));
            }

            if ($participant->role === 'kabid') {
                if ($item->pegawai_id !== $participant->id || $item->detail_perjalanan_id !== $detail->id) {
                    $nomorSpd = $this->buildNomorSpd('KB', $year, $this->getNextNomorSpdSequence('KB', $year));
                }
            }

            DB::transaction(function () use ($item, $detail, $participant, $lamaHari, $nomorSpd) {
                $item->update([
                    'detail_perjalanan_id' => $detail->id,
                    'pegawai_id' => $participant->id,
                    'nomor_spd' => $nomorSpd,
                    'lama_hari' => $lamaHari,
                    'total_uang' => $lamaHari * $detail->uang_harian,
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Peserta SPD berhasil diperbarui.',
                'data' => $item->fresh(),
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Peserta SPD, detail perjalanan, atau pegawai tidak ditemukan.',
                'errors' => $e->getMessage(),
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui peserta SPD.',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $item = SpdPeserta::findOrFail($id);

            DB::transaction(function () use ($item) {
                $item->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Peserta SPD berhasil dihapus.',
                'data' => null,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Peserta SPD tidak ditemukan.',
                'errors' => $e->getMessage(),
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus peserta SPD.',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    private function getNextNomorSpdSequence(string $prefix, string $year): int
    {
        $pattern = sprintf('%s-%s-%%', $prefix, $year);
        $lastNomor = SpdPeserta::where('nomor_spd', 'like', $pattern)
            ->orderByDesc('nomor_spd')
            ->value('nomor_spd');

        if (! $lastNomor) {
            return 1;
        }

        $parts = explode('-', $lastNomor);

        return (int) end($parts) + 1;
    }

    private function buildNomorSpd(string $prefix, string $year, int $sequence): string
    {
        return sprintf('%s-%s-%04d', $prefix, $year, $sequence);
    }
}
