<?php

namespace App\Http\Controllers\Api\Manager;

use App\Http\Controllers\Api\Concerns\RoleCheck;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    use RoleCheck;

    public function index(Request $request)
    {
        $this->ensureRole($request, 'manager');

        $staff = User::where('role', 'staff')
            ->orderBy('name')
            ->paginate(10);

        return response()->json($staff);
    }

    public function store(Request $request)
    {
        $this->ensureRole($request, 'manager');

        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $staff = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => 'staff',
        ]);

        return response()->json([
            'message' => 'Staff baru berhasil dibuat.',
            'staff'   => $staff,
        ], 201);
    }

    public function show(Request $request, string $id)
    {
        $this->ensureRole($request, 'manager');

        $staff = User::where('role', 'staff')->findOrFail($id);

        return response()->json($staff);
    }

    public function update(Request $request, string $id)
    {
        $this->ensureRole($request, 'manager');

        $staff = User::where('role', 'staff')->findOrFail($id);

        $data = $request->validate([
            'name'     => ['sometimes', 'required', 'string', 'max:255'],
            'email'    => ['sometimes', 'required', 'email', 'max:255', 'unique:users,email,'.$staff->id],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        if (isset($data['password']) && $data['password']) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $staff->update($data);

        return response()->json([
            'message' => 'Data staff berhasil diperbarui.',
            'staff'   => $staff,
        ]);
    }

    public function destroy(Request $request, string $id)
    {
        $this->ensureRole($request, 'manager');

        $staff = User::where('role', 'staff')->findOrFail($id);
        $staff->delete();

        return response()->json([
            'message' => 'Staff berhasil dihapus.',
        ]);
    }
}
