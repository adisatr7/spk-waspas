<?php

namespace App\Http\Controllers\Api\Concerns;

use Illuminate\Http\Request;

trait RoleCheck
{
    protected function ensureRole(Request $request, string $role): void
    {
        $user = $request->user();

        if (! $user || $user->role !== $role) {
            abort(response()->json([
                'message' => 'Akses ditolak. Role yang dibutuhkan: '.$role,
            ], 403));
        }
    }
}
