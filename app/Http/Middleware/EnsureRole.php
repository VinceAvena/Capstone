<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * MIDDLEWARE: the gatekeeper for role-based access (the Security part of the
 * system). A route written as  EnsureRole::class.':admin,inspector'  lets in
 * only users whose role is "admin" or "inspector".
 *
 *  - not logged in        -> sent to the login page
 *  - account switched off -> logged out with a message
 *  - wrong role           -> 403 "Forbidden" page
 */
class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->guest(route('login'));
        }

        if (! $user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors(['email' => 'This account has been deactivated. Please contact the market administration office.']);
        }

        // A route that lists no role (":" with nothing after it) means "any logged-in user".
        $roles = array_values(array_filter($roles, fn ($r) => $r !== ''));

        if ($roles && ! in_array($user->role, $roles, true)) {
            abort(403, 'Your account type is not allowed to open this page.');
        }

        return $next($request);
    }
}
