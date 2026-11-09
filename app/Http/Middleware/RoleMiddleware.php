<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $identityKey = $role === 'Pelanggan' ? 'id_pelanggan' : 'id_petugas';
        $isAuthorized = $request->session()->get('role') === $role
            && ! is_null($request->session()->get($identityKey));

        if (! $isAuthorized) {
            $message = $request->session()->has('role')
                ? 'Akses tidak diizinkan.'
                : 'Silakan login terlebih dahulu.';

            return redirect()->route('login')->with('error', $message);
        }

        if (
            $role === 'Kurir'
            && (bool) $request->session()->get('must_change_password', false)
            && ! $request->routeIs(
                'kurir.password.change',
                'kurir.password.update-temporary',
                'kurir.logout'
            )
        ) {
            return redirect()->route('kurir.password.change');
        }

        return $next($request);
    }
}