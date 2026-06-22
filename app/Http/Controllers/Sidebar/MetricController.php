<?php

namespace App\Http\Controllers\Sidebar;

use App\Http\Controllers\Controller;
use App\Models\SidebarMetric;
use Illuminate\Http\Request;
use App\Rules\UniquePeriod;

class MetricController extends Controller
{

    protected $sidebarId = 6;

    public function index(Request $request)
    {
        $query = SidebarMetric::with('serviceType')->where('service_type_id', $this->sidebarId);

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
            'month'            => [
                'required',
                'integer',
                'min:1',
                'max:12',
                new UniquePeriod('sidebar_metrics', $request->year)
            ],
            'year'             => 'required|integer|min:2000|max:2099',
            'total_users'      => 'required|integer|min:0',
            'active_users'     => 'required|integer|min:0',
            'document_created' => 'required|integer|min:0',
        ]);

        $validated['service_type_id'] = $this->sidebarId;

        SidebarMetric::create($validated);

        return response()->json(['message' => 'Data berhasil ditambahkan.']);
    }

    public function edit($id)
    {
        $item = SidebarMetric::with('serviceType')->where('service_type_id', $this->sidebarId)->findOrFail($id);
        return response()->json(compact('item'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'month'            => [
                'required',
                'integer',
                'min:1',
                'max:12',
                new UniquePeriod('sidebar_metrics', $request->year, $id)
            ],
            'year'             => 'required|integer|min:2000|max:2099',
            'total_users'      => 'required|integer|min:0',
            'active_users'     => 'required|integer|min:0',
            'document_created' => 'required|integer|min:0',
        ]);

        $validated['service_type_id'] = $this->sidebarId;

        $item = SidebarMetric::with('serviceType')->where('service_type_id', $this->sidebarId)->findOrFail($id);
        $item->update($validated);

        return response()->json(['message' => 'Data berhasil diperbarui.']);
    }

    public function destroy($id)
    {
        $item = SidebarMetric::where('service_type_id', $this->sidebarId)
            ->where('id', $id)
            ->firstOrFail();

        $item->delete();

        return response()->json(['message' => 'Data berhasil dihapus.']);
    }
}
