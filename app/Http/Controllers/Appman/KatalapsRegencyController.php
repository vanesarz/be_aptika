<?php

namespace App\Http\Controllers\Appman;

use App\Http\Controllers\Controller;
use App\Models\AppmanKatalapsRegency;
use Illuminate\Http\Request;
use App\Rules\UniquePeriod;

class KatalapsRegencyController extends Controller
{
    protected $appmanId = 5;

    public function index(Request $request)
    {
        $query = AppmanKatalapsRegency::where('service_type_id', $this->appmanId)
            ->with('regency');

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        $data = $query->orderByDesc('year')->orderByDesc('month')->get();

        return response()->json(compact('data'));
    }

    public function create()
    {
        $regencies = \App\Models\RegencyName::all();
        return response()->json(compact('regencies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'regency_id' => 'required|exists:regencies_name,id',
            'month' => [
                'required',
                'integer',
                'min:1',
                'max:12',
                new UniquePeriod('appman_katalaps_regencies', $request->year, null, 'regency_id', $request->regency_id)
            ],
            'year' => 'required|integer|min:2000|max:2099',
            'app_count' => 'required|integer|min:0',
        ]);

        $validated['service_type_id'] = $this->appmanId;

        AppmanKatalapsRegency::create($validated);

        return response()->json(['message' => 'Data berhasil ditambahkan.']);
    }

    public function edit($id)
    {
        $item = AppmanKatalapsRegency::findOrFail($id);
        $regencies = \App\Models\RegencyName::all();
        return response()->json(compact('item', 'regencies'));
    }

    public function update(Request $request, $id)
    {
        $item = AppmanKatalapsRegency::findOrFail($id);

        $validated = $request->validate([
            'regency_id' => 'required|exists:regencies_name,id',
            'month' => [
                'required',
                'integer',
                'min:1',
                'max:12',
                new UniquePeriod('appman_katalaps_regencies', $request->year, $id, 'regency_id', $request->regency_id)
            ],
            'year' => 'required|integer|min:2000|max:2099',
            'app_count' => 'required|integer|min:0',
        ]);

        $item->update($validated);

        return response()->json(['message' => 'Data berhasil diperbarui.']);
    }

    public function destroy($id)
    {
        $item = AppmanKatalapsRegency::findOrFail($id);
        $item->delete();

        return response()->json(['message' => 'Data berhasil dihapus.']);
    }
}
