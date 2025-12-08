<?php

namespace App\Http\Controllers\Api\Manager;

use App\Http\Controllers\Api\Concerns\RoleCheck;
use App\Http\Controllers\Controller;
use App\Models\Criterion;
use App\Models\Influencer;
use App\Models\User;
use App\Models\WaspasHistory;
use Illuminate\Http\Request;

class WaspasResultController extends Controller
{
    use RoleCheck;

    public function dashboard(Request $request)
    {
        $this->ensureRole($request, 'manager');

        $totalInfluencers = Influencer::count();
        $totalCriteria    = Criterion::where('is_active', true)->count();
        $totalHistories   = WaspasHistory::count();
        $staffCount       = User::where('role', 'staff')->count();

        return response()->json([
            'total_influencers' => $totalInfluencers,
            'total_criteria'    => $totalCriteria,
            'total_histories'   => $totalHistories,
            'staff_count'       => $staffCount,
        ]);
    }

    public function index(Request $request)
    {
        $this->ensureRole($request, 'manager');

        $histories = WaspasHistory::with(['user'])
            ->withCount([
                'items as influencer_count',
                'items as selected_count' => function ($q) {
                    $q->where('selected', true);
                },
            ])
            ->orderByDesc('created_at')
            ->paginate(10);

        return response()->json($histories);
    }

    public function show(Request $request, string $id)
    {
        $this->ensureRole($request, 'manager');

        $history = WaspasHistory::with([
                'user',
                'items.influencer',
            ])
            ->findOrFail($id);

        return response()->json($history);
    }
}
