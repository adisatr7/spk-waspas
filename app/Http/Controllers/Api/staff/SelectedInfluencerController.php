<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Api\Concerns\RoleCheck;
use App\Http\Controllers\Controller;
use App\Models\WaspasHistoryItem;
use Illuminate\Http\Request;

class SelectedInfluencerController extends Controller
{
    use RoleCheck;

    public function selectedList(Request $request)
    {
        $this->ensureRole($request, 'staff');

        $user = $request->user();

        $items = WaspasHistoryItem::with(['influencer', 'history'])
            ->where('selected', true)
            ->whereHas('history', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->orderByDesc('updated_at')
            ->paginate(10);

        return response()->json($items);
    }

    public function toggle(Request $request, string $id)
    {
        $this->ensureRole($request, 'staff');

        $user = $request->user();

        $item = WaspasHistoryItem::whereHas('history', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->findOrFail($id);

        $item->selected = ! $item->selected;
        $item->save();

        return response()->json([
            'message' => $item->selected
                ? 'Influencer ditandai dipilih (endorse).'
                : 'Influencer batal dipilih.',
            'item'    => $item,
        ]);
    }
}
