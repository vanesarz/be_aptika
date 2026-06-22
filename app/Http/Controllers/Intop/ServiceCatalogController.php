<?php

namespace App\Http\Controllers\Intop;

use App\Http\Controllers\Controller;
use App\Models\IntopServiceCatalog;
use Illuminate\Http\Request;
use App\Rules\UniquePeriod;

class ServiceCatalogController extends Controller
{
    protected $intopId = 4;

    public function index(Request $request)
    {
        $query = IntopServiceCatalog::query();

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        $data = $query->latest('id')
            ->get()
            ->map(fn($item) => $this->withPercentage($item));

        return response()->json($data);
    }

    public function create()
    {
        return response()->json(['message' => 'Success']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'month'                => [
                'required',
                'integer',
                'min:1',
                'max:12',
                new UniquePeriod('intop_service_catalogs', $request->year)
            ],
            'year'                 => 'required|integer|min:2000|max:2099',
            'adm_service_count'    => 'required|integer|min:0',
            'public_service_count' => 'required|integer|min:0',
            'target_abs'           => 'required|numeric|min:0',
            'achievement_abs'      => 'required|numeric|min:0',
        ]);

        $validated['service_type_id'] = $this->intopId;

        IntopServiceCatalog::create($validated);

        return response()->json(['message' => 'Data berhasil ditambahkan.']);
    }

    public function edit($id)
    {
        $item = IntopServiceCatalog::findOrFail($id);

        return response()->json($this->withPercentage($item));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'month'                => [
                'required',
                'integer',
                'min:1',
                'max:12',
                new UniquePeriod('intop_service_catalogs', $request->year, $id)
            ],
            'year'                 => 'required|integer|min:2000|max:2099',
            'adm_service_count'    => 'required|integer|min:0',
            'public_service_count' => 'required|integer|min:0',
            'target_abs'           => 'required|numeric|min:0',
            'achievement_abs'      => 'required|numeric|min:0',
        ]);

        $validated['service_type_id'] = $this->intopId;

        IntopServiceCatalog::findOrFail($id)->update($validated);

        return response()->json(['message' => 'Data berhasil diperbarui.']);
    }

    public function destroy($id)
    {
        IntopServiceCatalog::findOrFail($id)->delete();

        return response()->json(['message' => 'Data berhasil dihapus.']);
    }

    // ─── fungsi hitung presentase ───────────────────────────────────────────────────────────────
    private function withPercentage(IntopServiceCatalog $item): array
    {
        $total = $item->adm_service_count + $item->public_service_count;

        return [
            ...$item->toArray(),
            'target_percentage'         => (float) $item->target_abs,
            'achievement_percentage'    => (float) $item->achievement_abs,
        ];
    }
}
