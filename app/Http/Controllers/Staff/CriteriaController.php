<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Criterion;
use Illuminate\Http\Request;

class CriteriaController extends Controller
{
    /**
     * Pastikan user role = staff.
     */
    private function ensureStaff(Request $request): void
    {
        /** @var \App\Models\User|null $user */
        $user = $request->user();

        if (!$user || $user->role !== 'staff') {
            abort(403, 'Akses ditolak. Hanya staff yang boleh mengelola kriteria.');
        }
    }

    /**
     * List kriteria.
     */
    public function index(Request $request)
    {
        $this->ensureStaff($request);

        $query = Criterion::query()->orderBy('code');

        $search = $request->get('q');
        if ($search) {
            $query->where(function ($q2) use ($search) {
                $q2->where('code', 'like', '%' . $search . '%')
                    ->orWhere('name', 'like', '%' . $search . '%');
            });
        }

        $criteria = $query->paginate(10)->withQueryString();

        // Data lengkap untuk tampilan ringkasan (Data Kriteria & Bobot)
        $allCriteria = Criterion::orderBy('code')->get();

        // Total bobot aktif (buat informasi)
        $totalWeight = Criterion::where('is_active', true)->sum('weight');

        return view('staff.criteria.index', compact('criteria', 'search', 'totalWeight', 'allCriteria'));
    }

    /**
     * Form tambah kriteria.
     */
    public function create(Request $request)
    {
        $this->ensureStaff($request);

        return view('staff.criteria.create');
    }

    /**
     * Simpan kriteria baru.
     */
    public function store(Request $request)
    {
        $this->ensureStaff($request);

        $data = $request->validate([
            'code'        => ['required', 'string', 'max:20', 'unique:criteria,code'],
            'name'        => ['required', 'string', 'max:255'],
            'type'        => ['required', 'in:benefit,cost'],
            'weight'      => ['required', 'numeric', 'min:0', 'max:1'],
            'description' => ['nullable', 'string'],
            'is_active'   => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        Criterion::create($data);

        return redirect()
            ->route('staff.criteria.index')
            ->with('success', 'Kriteria baru berhasil ditambahkan.');
    }

    /**
     * Form edit kriteria.
     */
    public function edit(Request $request, string $id)
    {
        $this->ensureStaff($request);

        $criterion = Criterion::findOrFail($id);

        return view('staff.criteria.edit', compact('criterion'));
    }

    /**
     * Update kriteria.
     */
    public function update(Request $request, string $id)
    {
        $this->ensureStaff($request);

        $criterion = Criterion::findOrFail($id);

        $data = $request->validate([
            'code'        => ['required', 'string', 'max:20', 'unique:criteria,code,' . $criterion->id],
            'name'        => ['required', 'string', 'max:255'],
            'type'        => ['required', 'in:benefit,cost'],
            'weight'      => ['required', 'numeric', 'min:0', 'max:1'],
            'description' => ['nullable', 'string'],
            'is_active'   => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        $criterion->update($data);

        return redirect()
            ->route('staff.criteria.index')
            ->with('success', 'Kriteria berhasil diperbarui.');
    }

    /**
     * Hapus kriteria.
     */
    public function destroy(Request $request, string $id)
    {
        $this->ensureStaff($request);

        $criterion = Criterion::findOrFail($id);
        $criterion->delete();

        return redirect()
            ->route('staff.criteria.index')
            ->with('success', 'Kriteria berhasil dihapus.');
    }
}
