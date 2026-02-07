// Create file: app/Http/Middleware/DebugRequests.php

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DebugRequests
{
    public function handle(Request $request, Closure $next)
    {
        // Log before
        Log::channel('single')->info('===== REQUEST START =====');
        Log::channel('single')->info('Method: ' . $request->method());
        Log::channel('single')->info('URL: ' . $request->fullUrl());
        Log::channel('single')->info('Path: ' . $request->path());
        Log::channel('single')->info('Input: ' . json_encode($request->all()));
        Log::channel('single')->info('Headers: ' . json_encode($request->headers->all()));

        $response = $next($request);

        // Log after
        Log::channel('single')->info('Response Status: ' . $response->status());
        Log::channel('single')->info('===== REQUEST END =====');

        return $response;
    }
}