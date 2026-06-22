<?php

namespace App\Http\Controllers\SadaJabar;

use App\Http\Controllers\Controller;
use App\Models\SadajabarAppIntegration;
use App\Models\GeneralInstitutionCategory;
use Illuminate\Http\Request;
use App\Rules\UniquePeriod;

class AppIntegrationController extends Controller
{
    protected $sadajabarId = 1;

    public function index(Request $request)
    {
        $query = SadajabarAppIntegration::with('institutionCategory')
            ->where('service_type_id', $this->sadajabarId);

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        $data = $query->orderByDesc('year')->orderByDesc('month')->get();

        return response()->json(compact('data'));
    }

    public function create()
    {
        $categories = GeneralInstitutionCategory::all();
        return response()->json(compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'year'                                => 'required|integer|min:2000|max:2099',
            'month'                               => [
                'required',
                'integer',
                'min:1',
                'max:12',
                new UniquePeriod('sadajabar_app_integrations', $request->year, null, 'institution_id', $request->institution_id)
            ],
            'app_count'                           => 'required|integer|min:0',
            'institution_id' => 'required|exists:general_institution_categories,id',
        ]);
        $validated['service_type_id'] = $this->sadajabarId;

        SadajabarAppIntegration::create($validated);

        return response()->json(['message' => 'Data integrasi berhasil ditambahkan.']);
    }

    public function edit($id)
    {
        $item       = SadajabarAppIntegration::findOrFail($id);
        $categories = GeneralInstitutionCategory::all();
        return response()->json(compact('item', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $item      = SadajabarAppIntegration::findOrFail($id);
        $validated = $request->validate([
            'year'                                => 'required|integer|min:2000|max:2099',
            'month'                               => [
                'required',
                'integer',
                'min:1',
                'max:12',
                new UniquePeriod('sadajabar_app_integrations', $request->year, $id, 'institution_id', $request->institution_id)
            ],
            'app_count'                           => 'required|integer|min:0',
            'institution_id' => 'required|exists:general_institution_categories,id',
        ]);
        $item->update($validated);

        return response()->json(['message' => 'Data integrasi berhasil diperbarui.']);
    }

    public function destroy($id)
    {
        SadajabarAppIntegration::findOrFail($id)->delete();
        return response()->json(['message' => 'Data integrasi berhasil dihapus.']);
    }
}
