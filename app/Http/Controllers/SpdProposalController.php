<?php

namespace App\Http\Controllers;

use App\Models\SpdProposal;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class SpdProposalController extends Controller
{
    public function index(Request $request)
    {
        $query = SpdProposal::query()->where('user_id', $request->user()->id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('employee_name', 'like', "%{$search}%")
                    ->orWhere('purpose', 'like', "%{$search}%")
                    ->orWhere('destination', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $data = $query->orderByDesc('created_at')->get();

        return response()->json(compact('data'));
    }

    public function stats(Request $request)
    {
        $query = SpdProposal::query()->where('user_id', $request->user()->id);

        $total = (clone $query)->count();
        $byStatus = (clone $query)->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $driver = DB::getDriverName();
        $monthExpression = $driver === 'sqlite'
            ? "strftime('%Y-%m', start_date)"
            : "DATE_FORMAT(start_date, '%Y-%m')";

        $monthly = (clone $query)->selectRaw("{$monthExpression} as month")
            ->selectRaw('count(*) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json([
            'data' => [
                'total' => $total,
                'by_status' => $byStatus->mapWithKeys(fn ($value, $key) => [$key => (int) $value])->toArray(),
                'monthly' => $monthly->map(fn ($item) => [
                    'month' => $item->month,
                    'total' => (int) $item->total,
                ])->values(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);

        $validated['user_id'] = $request->user()->id;
        $validated['followers'] = $this->normalizeFollowers($request->input('followers', []));

        $item = SpdProposal::create($validated);

        return response()->json(['message' => 'Data SPD berhasil disimpan.', 'data' => $item], 201);
    }

    public function show(Request $request, $id)
    {
        $item = SpdProposal::where('user_id', $request->user()->id)->findOrFail($id);

        return response()->json(['data' => $item]);
    }

    public function update(Request $request, $id)
    {
        $item = SpdProposal::where('user_id', $request->user()->id)->findOrFail($id);

        $validated = $this->validateRequest($request, $item);
        $validated['followers'] = $this->normalizeFollowers($request->input('followers', $item->followers));

        $item->update($validated);

        return response()->json(['message' => 'Data SPD berhasil diperbarui.', 'data' => $item->fresh()]);
    }

    public function destroy(Request $request, $id)
    {
        $item = SpdProposal::where('user_id', $request->user()->id)->findOrFail($id);
        $item->delete();

        return response()->json(['message' => 'Data SPD berhasil dihapus.']);
    }

    protected function validateRequest(Request $request, ?SpdProposal $item = null): array
    {
        $isUpdate = $item !== null;

        return $request->validate([
            'orderer_name' => $isUpdate ? 'nullable|string|max:255' : 'required|string|max:255',
            'orderer_nip' => 'nullable|string|max:255',
            'orderer_position' => 'nullable|string|max:255',
            'employee_name' => $isUpdate ? 'nullable|string|max:255' : 'required|string|max:255',
            'employee_nip' => 'nullable|string|max:255',
            'employee_rank' => 'nullable|string|max:255',
            'employee_position' => 'nullable|string|max:255',
            'purpose' => $isUpdate ? 'nullable|string' : 'required|string',
            'transportation' => 'nullable|string|max:255',
            'departure_place' => 'nullable|string|max:255',
            'destination' => 'nullable|string|max:255',
            'start_date' => $isUpdate ? 'nullable|date' : 'required|date',
            'end_date' => $isUpdate ? 'nullable|date|after_or_equal:start_date' : 'required|date|after_or_equal:start_date',
            'budget_estimate' => 'nullable|integer|min:0',
            'status' => 'nullable|in:draft,submitted,approved,in_progress,completed',
        ]);
    }

    protected function normalizeFollowers(mixed $followers): array
    {
        if (!is_array($followers)) {
            return [];
        }

        return array_values(array_filter(array_map(function ($follower) {
            if (!is_array($follower)) {
                return null;
            }

            return Arr::only($follower, ['name', 'nip', 'position']);
        }, $followers), fn ($value) => $value !== null));
    }
}
