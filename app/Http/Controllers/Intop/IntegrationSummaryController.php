<?php

namespace App\Http\Controllers\Intop;

use App\Http\Controllers\Controller;
use App\Models\IntopIntegrationSummary;
use Illuminate\Http\Request;
use App\Rules\UniquePeriod;

class IntegrationSummaryController extends Controller
{
    protected $intopId = 4;

    public function index(Request $request)
    {
        $query = IntopIntegrationSummary::with('serviceType')->where('service_type_id', $this->intopId);

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
            'institution_id'  => 'required|integer',
            'month'           => [
                'required',
                'integer',
                'min:1',
                'max:12',
                new UniquePeriod('intop_integration_summaries', $request->year, null, 'institution_id', $request->institution_id)
            ],
            'year'            => 'required|integer|min:2000|max:2099',
            'app_count'       => 'required|integer|min:0',
        ]);

        $validated['service_type_id'] = $this->intopId;

        IntopIntegrationSummary::create($validated);

        return response()->json(['message' => 'Data berhasil ditambahkan.']);
    }

    public function edit($id)
    {
        $item = IntopIntegrationSummary::with('serviceType')->where('service_type_id', $this->intopId)->findOrFail($id);
        return response()->json(compact('item'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'institution_id'  => 'required|integer',
            'month'           => [
                'required',
                'integer',
                'min:1',
                'max:12',
                new UniquePeriod('intop_integration_summaries', $request->year, $id, 'institution_id', $request->institution_id)
            ],
            'year'            => 'required|integer|min:2000|max:2099',
            'app_count'       => 'required|integer|min:0',
        ]);

        $item = IntopIntegrationSummary::with('serviceType')->where('service_type_id', $this->intopId)->findOrFail($id);
        $item->update($validated);
        $validated['service_type_id'] = $this->intopId;

        return response()->json(['message' => 'Data berhasil diperbarui.']);
    }

    public function destroy($id)
    {
        $item = IntopIntegrationSummary::where('service_type_id', $this->intopId)
            ->where('id', $id) // Kunci utama: filter berdasarkan ID unik
            ->firstOrFail();

        $item->delete();

        return response()->json(['message' => 'Data berhasil dihapus.']);
    }
}
