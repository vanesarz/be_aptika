<?php

namespace App\Http\Controllers\SmartJabar;

use App\Http\Controllers\Controller;
use App\Models\SmartjabarJoinedApp;
use Illuminate\Http\Request;
use App\Rules\UniquePeriod;

class JoinedAppController extends Controller
{
    protected $smartJabarId = 2;

    public function index(Request $request)
    {
        $query = SmartjabarJoinedApp::where('service_type_id', $this->smartJabarId);

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        $data = $query->orderByDesc('year')->orderByDesc('month')->get();

        return response()->json(compact('data'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'year'       => 'required|integer|min:2000|max:2099',
            'month'      => [
                'required',
                'integer',
                'min:1',
                'max:12',
                new UniquePeriod('smartjabar_joined_apps', $request->year)
            ],
            'total_apps' => 'required|integer|min:0',
        ]);

        $validated['service_type_id'] = $this->smartJabarId;

        SmartjabarJoinedApp::create($validated);

        return response()->json(['message' => 'Data berhasil ditambahkan.']);
    }

    public function edit($id)
    {
        $item = SmartjabarJoinedApp::findOrFail($id);
        return response()->json(compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = SmartjabarJoinedApp::findOrFail($id);

        $validated = $request->validate([
            'year'       => 'required|integer|min:2000|max:2099',
            'month'      => [
                'required',
                'integer',
                'min:1',
                'max:12',
                new UniquePeriod('smartjabar_joined_apps', $request->year, $id)
            ],
            'total_apps' => 'required|integer|min:0',
        ]);

        $item->update($validated);

        return response()->json(['message' => 'Data berhasil diperbarui.']);
    }

    public function destroy($id)
    {
        $item = SmartjabarJoinedApp::findOrFail($id);
        $item->delete();

        return response()->json(['message' => 'Data berhasil dihapus.']);
    }
}
