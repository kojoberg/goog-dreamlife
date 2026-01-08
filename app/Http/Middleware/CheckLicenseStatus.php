<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Setting;
use Carbon\Carbon;

class CheckLicenseStatus
{
    /**
     * Routes that are always allowed even when license expired.
     */
    protected array $alwaysAllowedRoutes = [
        'settings.index',
        'settings.update',
        'settings.system_update',
        'logout',
        'login',
        'register',
        'password.request',
        'password.email',
        'password.reset',
        'password.update',
        'password.confirm',
        'verification.notice',
        'verification.verify',
        'verification.send',
        'profile.edit',
        'profile.update',
        'notifications.latest',
        'notifications.read',
        'notifications.mark-all',
    ];

    /**
     * URL paths that are always allowed (for routes without names).
     */
    protected array $alwaysAllowedPaths = [
        'login',
        'register',
        'logout',
        'forgot-password',
        'reset-password',
        'verify-email',
        'confirm-password',
        'settings',
    ];

    /**
     * Routes blocked entirely when license expired (not even GET).
     */
    protected array $blockedRoutes = [
        'pos.index',
        'pos.store',
        'pos.check-interactions',
        'shifts.create',
        'shifts.store',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip during testing
        if (app()->runningUnitTests()) {
            return $next($request);
        }

        // Check if license is expired
        if ($this->isLicenseExpired()) {
            $routeName = $request->route()?->getName();
            $path = $request->path();

            // Always allow certain routes by name
            if ($routeName && $this->isRouteAllowed($routeName)) {
                session()->flash('license_warning', 'Your license has expired. The system is in read-only mode. Please renew your license in Settings.');
                return $next($request);
            }

            // Always allow certain paths (for auth routes without names)
            if ($this->isPathAllowed($path)) {
                session()->flash('license_warning', 'Your license has expired. The system is in read-only mode. Please renew your license in Settings.');
                return $next($request);
            }

            // Block specific routes entirely
            if ($routeName && $this->isRouteBlocked($routeName)) {
                return $this->blockAccess($request, 'This feature is disabled. Your license has expired.');
            }

            // Block all write operations (POST, PUT, PATCH, DELETE)
            if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
                return $this->blockAccess($request, 'Cannot perform this action. Your license has expired. Please renew in Settings.');
            }

            // Allow GET requests but add warning
            session()->flash('license_warning', 'Your license has expired. The system is in read-only mode. Please renew your license in Settings.');
        }

        return $next($request);
    }

    /**
     * Check if the license (or trial) has expired.
     */
    protected function isLicenseExpired(): bool
    {
        $settings = Setting::first();

        if (!$settings) {
            return false; // No settings = new install, allow access
        }

        // If license_expiry is set, check it
        if ($settings->license_expiry) {
            $expiry = Carbon::parse($settings->license_expiry);
            return $expiry->isPast();
        }

        // No license = trial mode, check 14-day trial
        $installDate = $settings->created_at ?? Carbon::now();
        $trialEndsAt = $installDate->copy()->addDays(14);

        return $trialEndsAt->isPast();
    }

    /**
     * Check if route is always allowed.
     */
    protected function isRouteAllowed(string $routeName): bool
    {
        foreach ($this->alwaysAllowedRoutes as $allowed) {
            if ($routeName === $allowed || str_starts_with($routeName, rtrim($allowed, '*'))) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if URL path is always allowed.
     */
    protected function isPathAllowed(string $path): bool
    {
        foreach ($this->alwaysAllowedPaths as $allowedPath) {
            if ($path === $allowedPath || str_starts_with($path, $allowedPath . '/')) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if route is completely blocked.
     */
    protected function isRouteBlocked(string $routeName): bool
    {
        return in_array($routeName, $this->blockedRoutes);
    }

    /**
     * Return appropriate block response.
     */
    protected function blockAccess(Request $request, string $message): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'license_expired',
                'message' => $message
            ], 403);
        }

        return redirect()
            ->back()
            ->with('error', $message);
    }
}
