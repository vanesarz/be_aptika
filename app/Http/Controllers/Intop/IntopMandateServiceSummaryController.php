<?php

namespace App\Http\Controllers\Intop;

use App\Http\Controllers\Controller;
use App\Models\IntopMandateServiceSummary;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class IntopMandateServiceSummaryController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        $query = IntopMandateServiceSummary::query();

        if ($request->filled('year')) {
            $query->byYear((int) $request->year);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $data = $query->orderBy('year', 'desc')->get();

        return response()->json([
            'data' => $data,
        ]);
    }


    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'year'         => 'required|integer|min:2000|max:2100',
            'category'     => ['required', Rule::in(['administrasi', 'publik'])],
            'service_name' => 'required|string|max:255',
        ]);

        $record = IntopMandateServiceSummary::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disimpan.',
            'data'    => $record,
        ], 201);
    }


    public function show(int $id): JsonResponse
    {
        $record = IntopMandateServiceSummary::findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $record,
        ]);
    }


    public function update(Request $request, int $id): JsonResponse
    {
        $record = IntopMandateServiceSummary::findOrFail($id);

        $validated = $request->validate([
            'year'         => 'sometimes|integer|min:2000|max:2100',
            'category'     => ['sometimes', Rule::in(['administrasi', 'publik'])],
            'service_name' => 'sometimes|string|max:255',
        ]);

        $record->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diperbarui.',
            'data'    => $record,
        ]);
    }


    public function destroy(int $id): JsonResponse
    {
        $record = IntopMandateServiceSummary::findOrFail($id);
        $record->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil dihapus.',
        ]);
    }
}
