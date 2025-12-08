<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Api\Concerns\RoleCheck;
use App\Http\Controllers\Controller;
use App\Models\Criterion;
use Illuminate\Http\Request;

class CriteriaController extends Controller
{
    use RoleCheck;

    public function index(Request $request)
    {
        $this->ensureRole($request, 'staff');

        $criteria = Criterion::orderBy('code')->get();

        return response()->json($criteria);
    }

    public function store(Request $request)
    {
        $this->ensureRole($request, 'staff');

        $data = $request->validate([
            'code'      => ['required', 'string', 'max:10'],
            'name'      => ['required', 'string', 'max:255'],
            'weight'    => ['required', 'numeric', 'min:0'],
            'type'      => ['required', 'in:benefit,cost'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $data['is_active'] ?? true;

        $criterion = Criterion::create($data);

        return response()->json([
            'message'   => 'Kriteria berhasil dibuat.',
            'criterion' => $criterion,
        ], 201);
    }

    public function show(Request $request, string $id)
    {
        $this->ensureRole($request, 'staff');

        $criterion = Criterion::findOrFail($id);

        return response()->json($criterion);
    }

    public function update(Request $request, string $id)
    {
        $this->ensureRole($request, 'staff');

        $criterion = Criterion::findOrFail($id);

        $data = $request->validate([
            'code'      => ['sometimes', 'required', 'string', 'max:10'],
            'name'      => ['sometimes', 'required', 'string', 'max:255'],
            'weight'    => ['sometimes', 'required', 'numeric', 'min:0'],
            'type'      => ['sometimes', 'required', 'in:benefit,cost'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $criterion->update($data);

        return response()->json([
            'message'   => 'Kriteria berhasil diperbarui.',
            'criterion' => $criterion,
        ]);
    }

    public function destroy(Request $request, string $id)
    {
        $this->ensureRole($request, 'staff');

        $criterion = Criterion::findOrFail($id);
        $criterion->delete();

        return response()->json([
            'message' => 'Kriteria berhasil dihapus.',
        ]);
    }
}
