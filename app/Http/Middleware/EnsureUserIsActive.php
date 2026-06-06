<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // If user is pending or rejected, redirect to status page
            // EXCEPT if they are already ON the page, logging out, or resetting account
            if (
                in_array($user->status, ['pending', 'rejected'])
                && !$request->is('pending-approval')
                && !$request->is('account-reset')
                && !$request->is('logout')
                && !$request->is('assets/*') // Allow assets
            ) {
                return redirect()->route('pages-pending-approval');
            }

            // If user is suspended/inactive
            if ($user->status === 'inactive') {
                Auth::logout();
                return redirect()->route('login')->with('error', 'Akun Anda dinonaktifkan.');
            }
        }

        return $next($request);
    }
}
