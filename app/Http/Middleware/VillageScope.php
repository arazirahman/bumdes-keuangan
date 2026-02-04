<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VillageScope
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // superadmin: bebas (lihat semua)
        if ($user && $user->role === 'superadmin') {
            return $next($request);
        }

        // operator: wajib punya desa
        if ($user && $user->role === 'operator' && empty($user->village_id)) {
            abort(403, 'Akun operator belum diset Desa.');
        }

        // simpan untuk dipakai controller (opsional)
        if ($user) {
            $request->attributes->set('village_id', $user->village_id);
        }

        return $next($request);
    }
}
