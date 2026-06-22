<?php

namespace App\Http\Controllers\Appman;

use App\Http\Controllers\Controller;
use App\Models\AppmanInventoryStat;
use Illuminate\Http\Request;
use App\Rules\UniquePeriod;

class InventoryStatController extends Controller
{
    protected $appmanId = 5;

    public function index(Request $request)
    {
        $query = AppmanInventoryStat::where('service_type_id', $this->appmanId);

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        $data = $query->orderByDesc('year')->orderByDesc('month')->get();

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
                new UniquePeriod('appman_inventory_stats', $request->year)
            ],
            'year' => 'required|integer|min:2000|max:2099',
            'total_apps' => 'required|integer|min:0',
            'profile' => 'required|integer|min:0',
            'repository' => 'required|integer|min:0',
            'registered_pse' => 'required|integer|min:0',
        ]);
        
        $validated['service_type_id'] = $this->appmanId;
        
        AppmanInventoryStat::create($validated);
        
        return response()->json(['message' => 'Data berhasil ditambahkan.']);
    }

    public function edit($id)
    {
        $item = AppmanInventoryStat::findOrFail($id);
        return response()->json(compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = AppmanInventoryStat::findOrFail($id);
        
        $validated = $request->validate([
            'month' => [
                'required',
                'integer',
                'min:1',
                'max:12',
                new UniquePeriod('appman_inventory_stats', $request->year, $id)
            ],
            'year' => 'required|integer|min:2000|max:2099',
            'total_apps' => 'required|integer|min:0',
            'profile' => 'required|integer|min:0',
            'repository' => 'required|integer|min:0',
            'registered_pse' => 'required|integer|min:0',
        ]);
        
        $item->update($validated);
        
        return response()->json(['message' => 'Data berhasil diperbarui.']);
    }

    public function destroy($id)
    {
        $item = AppmanInventoryStat::findOrFail($id);
        $item->delete();
        
        return response()->json(['message' => 'Data berhasil dihapus.']);
    }
}