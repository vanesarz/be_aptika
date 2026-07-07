<?php

namespace App\Http\Controllers\SPD;

use App\Http\Controllers\Controller;
use App\Models\DetailPerjalanan;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DetailPerjalananController extends Controller
{
    public function index()
    {
        try {
            $items = DetailPerjalanan::with(['rekening', 'peserta', 'peserta.pegawai'])
                ->orderByDesc('created_at')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Data detail perjalanan berhasil diambil.',
                'data' => $items,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data detail perjalanan.',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $item = DetailPerjalanan::with(['rekening', 'peserta', 'peserta.pegawai'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Data detail perjalanan ditemukan.',
                'data' => $item,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Detail perjalanan tidak ditemukan.',
                'errors' => $e->getMessage(),
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data detail perjalanan.',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kegiatan' => 'required|string|max:255',
            'sub_kegiatan' => 'required|string|max:255',
            'tujuan' => 'required|string|max:255',
            'tanggal_berangkat' => 'required|date',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_berangkat',
            'uang_harian' => 'nullable|numeric|min:0',
            'rekening_id' => 'nullable|exists:rekening,id',
            'alat_angkutan' => 'nullable|string|max:100',
            'deskripsi' => 'nullable|string',
        ]);

        try {
            $item = DB::transaction(function () use ($validated) {
                $year = date('Y');
                $lastTravel = DetailPerjalanan::where('travel_code', 'like', "PD-{$year}-%")
                    ->orderByRaw('CAST(SUBSTRING_INDEX(travel_code, "-", -1) AS UNSIGNED) DESC')
                    ->first();
                $sequence = 1;
                if ($lastTravel) {
                    $parts = explode('-', $lastTravel->travel_code);
                    $sequence = (int)end($parts) + 1;
                }
                $travelCode = sprintf('PD-%s-%04d', $year, $sequence);

                // Ensure a default Rekening exists and assign it if not provided
                if (!isset($validated['rekening_id']) || empty($validated['rekening_id'])) {
                    $rekening = \App\Models\Rekening::first();
                    if (!$rekening) {
                        $rekening = \App\Models\Rekening::create([
                            'kode_rekening' => '5.1.02.04.01.0001',
                            'nomor_rekening' => '123-456-789',
                            'nama_rekening' => 'Belanja Perjalanan Dinas Biasa',
                        ]);
                    }
                    $validated['rekening_id'] = $rekening->id;
                }

                if (!isset($validated['uang_harian'])) {
                    $validated['uang_harian'] = 0;
                }
                if (!isset($validated['alat_angkutan'])) {
                    $validated['alat_angkutan'] = 'Kendaraan Dinas';
                }

                return DetailPerjalanan::create(array_merge($validated, [
                    'travel_code' => $travelCode,
                    'status' => 'belum_selesai',
                ]));
            });

            return response()->json([
                'success' => true,
                'message' => 'Detail perjalanan berhasil dibuat.',
                'data' => $item,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat detail perjalanan.',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'kegiatan' => 'required|string|max:255',
            'sub_kegiatan' => 'required|string|max:255',
            'tujuan' => 'required|string|max:255',
            'tanggal_berangkat' => 'required|date',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_berangkat',
            'uang_harian' => 'nullable|numeric|min:0',
            'rekening_id' => 'nullable|exists:rekening,id',
            'alat_angkutan' => 'nullable|string|max:100',
            'deskripsi' => 'nullable|string',
            'status' => 'nullable|in:belum_selesai,selesai',
        ]);

        try {
            $item = DetailPerjalanan::findOrFail($id);

            DB::transaction(function () use ($item, $validated) {
                $item->update($validated);
            });

            return response()->json([
                'success' => true,
                'message' => 'Detail perjalanan berhasil diperbarui.',
                'data' => $item->fresh(),
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Detail perjalanan tidak ditemukan.',
                'errors' => $e->getMessage(),
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui detail perjalanan.',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $item = DetailPerjalanan::findOrFail($id);

            DB::transaction(function () use ($item) {
                $item->peserta()->delete();
                $item->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Detail perjalanan berhasil dihapus.',
                'data' => null,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Detail perjalanan tidak ditemukan.',
                'errors' => $e->getMessage(),
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus detail perjalanan.',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:belum_selesai,selesai',
        ]);

        try {
            $item = DetailPerjalanan::findOrFail($id);

            $item->update(['status' => $validated['status']]);

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diperbarui.',
                'data' => $item,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Detail perjalanan tidak ditemukan.',
                'errors' => $e->getMessage(),
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status detail perjalanan.',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }
}
