<?php

namespace App\Install\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class InstallMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        //skip installer if not enabled
        if (!config('app.installer')) {
            return $next($request);
        }
        // Check if installation route and already installed
        if ($this->isInstalled() && $request->is('install*')) {
            return redirect('/');
        }

        // Check if non-installation route and not installed
        if (!$this->isInstalled() && !$request->is('install*')) {
            return redirect()->route('install.index');
        }

        return $next($request);
    }

    /**
     * Check if the application is installed by looking for the installed file
     */
    protected function isInstalled(): bool
    {
        return Storage::disk('public')->exists('installed');
    }

    /**
     * Mark the application as installed
     */
    public static function markAsInstalled(): bool
    {
        try {
            $content = json_encode([
                'installed_at' => now()->toIso8601String(),
                'version' => config('app.version', '1.0.0')
            ]);
            return file_put_contents(storage_path('app/public/installed'), $content) !== false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
