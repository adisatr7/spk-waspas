<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\RoleCheck;
use App\Models\Influencer;
use Illuminate\Http\Request;

class InfluencerController extends Controller
{
    use RoleCheck;

    public function index(Request $request)
    {
        $this->ensureRole($request, 'staff');

        $q = Influencer::query()->orderByDesc('created_at');

        if ($search = $request->query('q')) {
            $q->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('platform', 'like', "%{$search}%")
                    ->orWhere('niche', 'like', "%{$search}%");
            });
        }

        $perPage     = (int) $request->query('per_page', 10);
        $influencers = $q->paginate($perPage);

        return response()->json($influencers);
    }

    public function store(Request $request)
    {
        $this->ensureRole($request, 'staff');

        $data = $request->validate([
            'name'            => ['required', 'string', 'max:255'],
            'username'        => ['nullable', 'string', 'max:255'],
            'platform'        => ['required', 'string', 'max:100'],
            'followers'       => ['required', 'integer', 'min:0'],
            'engagement_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'niche'           => ['nullable', 'string', 'max:255'],
            'email'           => ['nullable', 'email', 'max:255'],
            'phone'           => ['nullable', 'string', 'max:50'],
            'profile_link'    => ['nullable', 'url', 'max:255'],
            'notes'           => ['nullable', 'string'],
        ]);

        $inf = Influencer::create($data);

        return response()->json([
            'message'    => 'Influencer berhasil dibuat.',
            'influencer' => $inf,
        ], 201);
    }

    public function show(Request $request, string $id)
    {
        $this->ensureRole($request, 'staff');

        $inf = Influencer::findOrFail($id);

        return response()->json($inf);
    }

    public function update(Request $request, string $id)
    {
        $this->ensureRole($request, 'staff');

        $inf = Influencer::findOrFail($id);

        $data = $request->validate([
            'name'            => ['sometimes', 'required', 'string', 'max:255'],
            'username'        => ['nullable', 'string', 'max:255'],
            'platform'        => ['sometimes', 'required', 'string', 'max:100'],
            'followers'       => ['sometimes', 'required', 'integer', 'min:0'],
            'engagement_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'niche'           => ['nullable', 'string', 'max:255'],
            'email'           => ['nullable', 'email', 'max:255'],
            'phone'           => ['nullable', 'string', 'max:50'],
            'profile_link'    => ['nullable', 'url', 'max:255'],
            'notes'           => ['nullable', 'string'],
        ]);

        $inf->update($data);

        return response()->json([
            'message'    => 'Influencer berhasil diperbarui.',
            'influencer' => $inf,
        ]);
    }

    public function destroy(Request $request, string $id)
    {
        $this->ensureRole($request, 'staff');

        $inf = Influencer::findOrFail($id);
        $inf->delete();

        return response()->json([
            'message' => 'Influencer berhasil dihapus.',
        ]);
    }
}
