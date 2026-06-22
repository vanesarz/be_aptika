<?php

namespace App\Http\Controllers\Appman;

use App\Http\Controllers\Controller;
use App\Models\AppmanIntegrationMapping;
use Illuminate\Http\Request;
use App\Rules\UniquePeriod;

class IntegrationMappingController extends Controller
{
    protected $appmanId = 5;

    public function index(Request $request)
    {
        $query = AppmanIntegrationMapping::where('service_type_id', $this->appmanId);

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        $data = $query->orderByDesc('year')->orderByDesc('month')->get();

        $data->map(function ($item) {
            $item->not_integrated = $item->integration_opportunity - $item->integrated;
            return $item;
        });

        return response()->json(compact('data'));
    }

    public function create()
    {
        return response()->json(['message' => 'Success']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'month' => [
                'required',
                'integer',
                'min:1',
                'max:12',
                new UniquePeriod('appman_integration_mappings', $request->year)
            ],
            'year' => 'required|integer|min:2000|max:2099',
            'total_apps' => 'required|integer|min:0',
            'integration_opportunity' => 'required|integer|min:0',
            'integrated' => 'required|integer|min:0',
        ]);

        $validated['service_type_id'] = $this->appmanId;

        AppmanIntegrationMapping::create($validated);

        return response()->json(['message' => 'Data berhasil ditambahkan.']);
    }

    public function edit($id)
    {
        $item = AppmanIntegrationMapping::findOrFail($id);
        return response()->json(compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = AppmanIntegrationMapping::findOrFail($id);

        $validated = $request->validate([
            'month' => [
                'required',
                'integer',
                'min:1',
                'max:12',
                new UniquePeriod('appman_integration_mappings', $request->year, $id)
            ],
            'year' => 'required|integer|min:2000|max:2099',
            'total_apps' => 'required|integer|min:0',
            'integration_opportunity' => 'required|integer|min:0',
            'integrated' => 'required|integer|min:0',
        ]);

        $item->update($validated);

        return response()->json(['message' => 'Data berhasil diperbarui.']);
    }

    public function destroy($id)
    {
        $item = AppmanIntegrationMapping::findOrFail($id);
        $item->delete();

        return response()->json(['message' => 'Data berhasil dihapus.']);
    }
}
