<?php

namespace App\Http\Controllers\SPD;

use App\Http\Controllers\Controller;
use App\Models\Rekening;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RekeningController extends Controller
{
    public function index()
    {
        try {
            $items = Rekening::orderBy('id')->get();

            return response()->json([
                'success' => true,
                'message' => 'Data rekening berhasil diambil.',
                'data' => $items,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data rekening.',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_rekening' => 'required|string|max:100',
            'nomor_rekening' => 'required|string|max:100',
            'nama_rekening' => 'required|string|max:255',
        ]);

        try {
            $item = DB::transaction(function () use ($validated) {
                return Rekening::create($validated);
            });

            return response()->json([
                'success' => true,
                'message' => 'Rekening berhasil dibuat.',
                'data' => $item,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat rekening.',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $item = Rekening::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Data rekening ditemukan.',
                'data' => $item,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Rekening tidak ditemukan.',
                'errors' => $e->getMessage(),
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data rekening.',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'kode_rekening' => 'required|string|max:100',
            'nomor_rekening' => 'required|string|max:100',
            'nama_rekening' => 'required|string|max:255',
        ]);

        try {
            $item = Rekening::findOrFail($id);

            DB::transaction(function () use ($item, $validated) {
                $item->update($validated);
            });

            return response()->json([
                'success' => true,
                'message' => 'Rekening berhasil diperbarui.',
                'data' => $item->fresh(),
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Rekening tidak ditemukan.',
                'errors' => $e->getMessage(),
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui rekening.',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $item = Rekening::findOrFail($id);

            DB::transaction(function () use ($item) {
                $item->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Rekening berhasil dihapus.',
                'data' => null,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Rekening tidak ditemukan.',
                'errors' => $e->getMessage(),
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus rekening.',
                'errors' => $e->getMessage(),
            ], 500);
        }
    }
}
