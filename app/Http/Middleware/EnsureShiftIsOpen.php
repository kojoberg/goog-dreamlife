<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureShiftIsOpen
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip for admin? Or maybe admins also need shifts? 
        // For simplicity, everyone who uses POS needs a shift.

        $user = $request->user();

        // Check if user has an open shift
        $openShift = \App\Models\Shift::where('user_id', $user->id)
            ->whereNull('end_time')
            ->first();

        if (!$openShift) {
            // Redirect to shift open page (we need to create this route/view)
            return redirect()->route('shifts.create')->with('error', 'You must open a shift to access the POS.');
        }

        // Share shift with request/view if needed?
        // $request->merge(['current_shift' => $openShift]);

        return $next($request);
    }
}
