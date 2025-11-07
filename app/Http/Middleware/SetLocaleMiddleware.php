<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get Accept-Language header
        $locale = $request->header('Accept-Language', 'en');

        // Extract language code (handle formats like "ar", "ar-SA", "en-US", etc.)
        $locale = strtolower(substr($locale, 0, 2));

        // Validate locale (only ar or en)
        if (!in_array($locale, ['ar', 'en'])) {
            $locale = 'en'; // Default to English
        }

        // Set application locale
        app()->setLocale($locale);

        return $next($request);
    }
}

