<?php

namespace App\Http\Controllers\Rekayasa;

use App\Http\Controllers\Controller;
use App\Models\RekayasaMentoringPerformance;
use Illuminate\Http\Request;
use App\Rules\UniquePeriod;

class MentoringPerformanceController extends Controller
{
    protected $rekayasaId = 3;

    public function index(Request $request)
    {
        $query = RekayasaMentoringPerformance::with('serviceType')->where('service_type_id', $this->rekayasaId);

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        $items = $query->latest('id')->get();

        return response()->json(compact('items'));
    }

    public function create()
    {
        return response()->json(['message' => 'Success']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'month'           => [
                'required',
                'integer',
                'min:1',
                'max:12',
                new UniquePeriod('rekayasa_mentoring_performance', $request->year)
            ],
            'year'            => 'required|integer|min:2000|max:2099',
            'total_apps'      => 'required|integer|min:0',
            'target'          => 'required|integer|min:0',
            'realization'     => 'required|integer|min:0',
        ]);

        $validated['service_type_id'] = $this->rekayasaId;

        RekayasaMentoringPerformance::create($validated);

        return response()->json(['message' => 'Data berhasil ditambahkan.']);
    }

    public function edit($id)
    {
        $item = RekayasaMentoringPerformance::with('serviceType')->where('service_type_id', $this->rekayasaId)->findOrFail($id);
        return response()->json(compact('item'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'month'           => [
                'required',
                'integer',
                'min:1',
                'max:12',
                new UniquePeriod('rekayasa_mentoring_performance', $request->year, $id)
            ],
            'year'            => 'required|integer|min:2000|max:2099',
            'total_apps'      => 'required|integer|min:0',
            'target'          => 'required|integer|min:0',
            'realization'     => 'required|integer|min:0',
        ]);

        $item = RekayasaMentoringPerformance::with('serviceType')->where('service_type_id', $this->rekayasaId)->findOrFail($id);
        $item->update($validated);
        $validated['service_type_id'] = $this->rekayasaId;

        return response()->json(['message' => 'Data berhasil diperbarui.']);
    }

    public function destroy($id)
    {

        $item = RekayasaMentoringPerformance::where('service_type_id', $this->rekayasaId)
            ->where('id', $id)
            ->firstOrFail();

        $item->delete();

        return response()->json(['message' => 'Data berhasil dihapus.']);
    }
}
