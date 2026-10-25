<?php

namespace App\Http\Middleware;

use App\Models\App;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyApiKey
{
    /**
     * Validate the X-API-Key header against the apps table.
     *
     * On success the authenticated App model (with its user relation)
     * is stored on the request so downstream code can access it via
     * $request->attributes->get('app').
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-Key');

        if (! $apiKey) {
            return response()->json([
                'success' => false,
                'code'    => 'MISSING_API_KEY',
                'message' => 'Header X-API-Key wajib disertakan.',
            ], 401);
        }

        $app = App::with('user')->where('key', $apiKey)->first();

        if (! $app) {
            return response()->json([
                'success' => false,
                'code'    => 'INVALID_API_KEY',
                'message' => 'API Key tidak valid.',
            ], 401);
        }

        // Attach the resolved App to the request for downstream use
        $request->attributes->set('app', $app);

        return $next($request);
    }
}
