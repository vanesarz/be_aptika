<?php

namespace App\Http\Controllers\Sidebar;

use App\Http\Controllers\Controller;
use App\Models\SidebarDocumentStat;
use Illuminate\Http\Request;
use App\Rules\UniquePeriod;

class DocumentStatController extends Controller
{
    protected $sidebarId = 6;

    public function index(Request $request)
    {
        $query = SidebarDocumentStat::with('serviceType')->where('service_type_id', $this->sidebarId);

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
            'document_type_id' => 'required|integer',
            'month'            => [
                'required',
                'integer',
                'min:1',
                'max:12',
                new UniquePeriod('sidebar_document_stats', $request->year, null, 'document_type_id', $request->document_type_id)
            ],
            'year'             => 'required|integer|min:2000|max:2099',
            'total_count'      => 'required|integer|min:0',
        ]);

        $validated['service_type_id'] = $this->sidebarId;

        SidebarDocumentStat::create($validated);

        return response()->json(['message' => 'Data berhasil ditambahkan.']);
    }

    public function edit($id)
    {
        $item = SidebarDocumentStat::with('serviceType')->where('service_type_id', $this->sidebarId)->findOrFail($id);
        return response()->json(compact('item'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'document_type_id' => 'required|integer',
            'month'            => [
                'required',
                'integer',
                'min:1',
                'max:12',
                new UniquePeriod('sidebar_document_stats', $request->year, $id, 'document_type_id', $request->document_type_id)
            ],
            'year'             => 'required|integer|min:2000|max:2099',
            'total_count'      => 'required|integer|min:0',
        ]);

        $item = SidebarDocumentStat::with('serviceType')->where('service_type_id', $this->sidebarId)->findOrFail($id);
        $item->update($validated);
        $validated['service_type_id'] = $this->sidebarId;

        return response()->json(['message' => 'Data berhasil diperbarui.']);
    }

    public function destroy($id)
    {

        $item = SidebarDocumentStat::where('service_type_id', $this->sidebarId)
            ->where('id', $id)
            ->firstOrFail();

        $item->delete();

        return response()->json(['message' => 'Data berhasil dihapus.']);
    }
}
