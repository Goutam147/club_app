<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Setting;
use Illuminate\Support\Facades\Schema;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            if (Schema::hasTable('settings')) {
                $maintenanceMode = Setting::where('key', 'maintenance_mode')->value('value');

                if ($maintenanceMode === '1') {
                    // Do not intercept if already on the maintenance page
                    if (!$request->is('maintenance')) {
                        return redirect()->route('maintenance');
                    }
                }
            }
        } catch (\Exception $e) {
            // Suppress errors if database is not ready
        }

        // If site is not in maintenance, redirect /maintenance back to home
        if ($request->is('maintenance')) {
            try {
                if (Schema::hasTable('settings')) {
                    $maintenanceMode = Setting::where('key', 'maintenance_mode')->value('value');
                    if ($maintenanceMode !== '1') {
                        return redirect('/');
                    }
                }
            } catch (\Exception $e) {
                return redirect('/');
            }
        }

        return $next($request);
    }
}
