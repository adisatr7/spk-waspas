<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Api\Concerns\RoleCheck;
use App\Http\Controllers\Controller;
use App\Models\Criterion;
use App\Models\Influencer;
use App\Models\WaspasHistory;
use App\Models\WaspasHistoryItem;
use Illuminate\Http\Request;

class WaspasController extends Controller
{
    use RoleCheck;

    // Dashboard statistik staff
    public function dashboard(Request $request)
    {
        $this->ensureRole($request, 'staff');

        $user = $request->user();

        $totalInfluencers = Influencer::count();
        $totalCriteria    = Criterion::where('is_active', true)->count();
        $totalHistories   = WaspasHistory::where('user_id', $user->id)->count();
        $totalSelected    = WaspasHistoryItem::where('selected', true)
            ->whereHas('history', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->count();

        return response()->json([
            'total_influencers' => $totalInfluencers,
            'total_criteria'    => $totalCriteria,
            'total_histories'   => $totalHistories,
            'total_selected'    => $totalSelected,
        ]);
    }

    public function index(Request $request)
    {
        $this->ensureRole($request, 'staff');

        $user = $request->user();

        $histories = WaspasHistory::withCount([
                'items as influencer_count',
                'items as selected_count' => function ($q) {
                    $q->where('selected', true);
                },
            ])
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(10);

        return response()->json($histories);
    }

    public function show(Request $request, string $id)
    {
        $this->ensureRole($request, 'staff');

        $user = $request->user();

        $history = WaspasHistory::with([
                'items.influencer',
            ])
            ->where('user_id', $user->id)
            ->findOrFail($id);

        return response()->json($history);
    }

    // NOTE:
    // store() untuk perhitungan WASPAS bisa kamu adaptasi dari
    // Staff\WaspasController (versi web) kalau mau expose via API juga.
}
