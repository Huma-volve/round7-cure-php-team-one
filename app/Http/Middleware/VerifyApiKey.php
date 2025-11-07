<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get API key from database (settings table) first, then fallback to config/env
        try {
            $expectedApiKey = Setting::getValue('maintenance_api_key') 
                           ?? config('app.maintenance_api_key');
        } catch (\Exception $e) {
            // If database is not available, use config/env
            $expectedApiKey = config('app.maintenance_api_key');
        }

        if (!$expectedApiKey) {
            return response()->json([
                'status' => false,
                'message' => 'Maintenance API key is not configured. Use POST /api/admin/server/generate-api-key to generate one.',
            ], 500);
        }

        // Get API key from request
        $apiKey = $request->header('X-API-Key') ?? $request->header('X-Api-Key');

        if (!$apiKey || $apiKey !== $expectedApiKey) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid or missing API key. Provide X-API-Key header with valid key.',
            ], 401);
        }

        return $next($request);
    }
}

