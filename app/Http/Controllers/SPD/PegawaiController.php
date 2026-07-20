<?php

namespace App\Http\Controllers\SPD;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PegawaiController extends Controller
{
    public function index()
    {
        try {
            $items = Pegawai::orderBy('nama')->get();

            return response()->json([
                'success' => true,
                'message' => 'Data pegawai berhasil diambil.',
                'data' => $items,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data pegawai.',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'          => 'required|string|max:255',
            'nip'           => 'required|string|max:18|unique:pegawai,nip',
            'pangkat'       => 'required|string|max:255',
            'jabatan'       => 'required|string|max:255',
            'role'          => 'required|in:kabid,staff',
            'tanggal_lahir' => 'nullable|date',
        ]);

        try {
            $item = DB::transaction(function () use ($validated) {
                if (empty($validated['tanggal_lahir'])) {
                    $validated['tanggal_lahir'] = '1990-01-01';
                }
                return Pegawai::create($validated);
            });

            return response()->json([
                'success' => true,
                'message' => 'Pegawai berhasil dibuat.',
                'data' => $item,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat pegawai.',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $item = Pegawai::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Data pegawai ditemukan.',
                'data' => $item,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Pegawai tidak ditemukan.',
                'errors' => $e->getMessage(),
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data pegawai.',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => 'required|string|max:18|unique:pegawai,nip,' . $id,
            'pangkat' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'role' => 'required|in:kabid,staff',
        ]);

        try {
            $item = Pegawai::findOrFail($id);

            DB::transaction(function () use ($item, $validated) {
                $item->update($validated);
            });

            return response()->json([
                'success' => true,
                'message' => 'Pegawai berhasil diperbarui.',
                'data' => $item->fresh(),
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Pegawai tidak ditemukan.',
                'errors' => $e->getMessage(),
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui pegawai.',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $item = Pegawai::findOrFail($id);

            DB::transaction(function () use ($item) {
                $item->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Pegawai berhasil dihapus.',
                'data' => null,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Pegawai tidak ditemukan.',
                'errors' => $e->getMessage(),
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus pegawai.',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }
}
