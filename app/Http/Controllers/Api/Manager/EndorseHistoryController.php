<?php

namespace App\Http\Controllers\Api\Manager;

use App\Http\Controllers\Api\Concerns\RoleCheck;
use App\Http\Controllers\Controller;
use App\Models\WaspasHistoryItem;
use Illuminate\Http\Request;

class EndorseHistoryController extends Controller
{
    use RoleCheck;

    public function index(Request $request)
    {
        $this->ensureRole($request, 'manager');

        $keyword = $request->query('q');

        $q = WaspasHistoryItem::with(['influencer', 'history.user'])
            ->where('selected', true)
            ->orderByDesc('updated_at');

        if ($keyword) {
            $q->whereHas('influencer', function ($builder) use ($keyword) {
                $builder->where('name', 'like', "%{$keyword}%")
                    ->orWhere('username', 'like', "%{$keyword}%")
                    ->orWhere('platform', 'like', "%{$keyword}%")
                    ->orWhere('niche', 'like', "%{$keyword}%");
            });
        }

        $items = $q->paginate(10);

        return response()->json($items);
    }
}
