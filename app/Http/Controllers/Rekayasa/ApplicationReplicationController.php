<?php

namespace App\Http\Controllers\Rekayasa;

use App\Http\Controllers\Controller;
use App\Models\RekayasaApplicationReplication;
use Illuminate\Http\Request;
use App\Rules\UniquePeriod;

class   ApplicationReplicationController extends Controller
{
    protected $rekayasaId = 3;

    public function index(Request $request)
    {
        $query = RekayasaApplicationReplication::with('serviceType')->where('service_type_id', $this->rekayasaId);

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        $data = $query->latest('id')->get();

        return response()->json(compact('data'));
    }

    public function create()
    {
        return response()->json(['message' => 'Success']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'institution_id'      => 'required|integer',
            'year'                => 'required|integer|min:2000|max:2099',
            'month'               => [
                'required',
                'integer',
                'min:1',
                'max:12',
                new UniquePeriod('rekayasa_application_replications', $request->year, null, 'institution_id', $request->institution_id)
            ],
            'total_replications'  => 'required|integer|min:0',
        ]);

        $validated['service_type_id'] = $this->rekayasaId;

        RekayasaApplicationReplication::create($validated);

        return response()->json(['message' => 'Data berhasil ditambahkan.']);
    }

    public function edit($id)
    {
        $item = RekayasaApplicationReplication::with('serviceType')->where('service_type_id', $this->rekayasaId)->findOrFail($id);
        return response()->json(compact('item'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'institution_id'      => 'required|integer',
            'year'                => 'required|integer|min:2000|max:2099',
            'month'               => [
                'required',
                'integer',
                'min:1',
                'max:12',
                new UniquePeriod('rekayasa_application_replications', $request->year, $id, 'institution_id', $request->institution_id)
            ],
            'total_replications'  => 'required|integer|min:0',
        ]);

        $item = RekayasaApplicationReplication::with('serviceType')->where('service_type_id', $this->rekayasaId)->findOrFail($id);
        $item->update($validated);
        $validated['service_type_id'] = $this->rekayasaId;

        return response()->json(['message' => 'Data berhasil diperbarui.']);
    }

    public function destroy($id)
    {
        $item = RekayasaApplicationReplication::where('service_type_id', $this->rekayasaId)
            ->where('id', $id)
            ->firstOrFail();

        $item->delete();

        return response()->json(['message' => 'Data berhasil dihapus.']);
    }

    // Endpoint untuk mendapatkan rekapitulasi data berdasarkan tahun dan bulan
    public function summary(Request $request)
    {
        $query = RekayasaApplicationReplication::where('service_type_id', $this->rekayasaId);

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        $data = $query->selectRaw('year, month, SUM(total_replications) as total')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'asc')
            ->get()
            ->groupBy('year')
            ->map(function ($months, $year) {
                return [
                    'year'        => $year,
                    'total_year'  => $months->sum('total'),
                    'months'      => $months->map(fn($m) => [
                        'month' => $m->month,
                        'total' => $m->total,
                    ]),
                ];
            })
            ->values();

        return response()->json(['data' => $data]);
    }
}
