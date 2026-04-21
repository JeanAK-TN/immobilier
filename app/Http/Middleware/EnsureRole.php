<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $allowedRoles = array_map(fn (string $r) => Role::from($r), $roles);

        if (! $request->user() || ! in_array($request->user()->role, $allowedRoles, strict: true)) {
            abort(403, 'Accès non autorisé.');
        }

        return $next($request);
    }
}
