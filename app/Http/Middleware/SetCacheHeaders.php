<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCacheHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Nur für statische Assets im /build-Verzeichnis
        if ($request->is('build/*')) {
            $extension = pathinfo($request->path(), PATHINFO_EXTENSION);
            
            // CSS und JS: 1 Jahr Cache (mit Content-Hash in Dateinamen)
            if (in_array($extension, ['css', 'js'])) {
                $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
            }
            // Bilder: 30 Tage Cache
            elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])) {
                $response->headers->set('Cache-Control', 'public, max-age=2592000');
            }
        }

        // Storage-Bilder: 7 Tage Cache
        if ($request->is('storage/*')) {
            $extension = pathinfo($request->path(), PATHINFO_EXTENSION);
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])) {
                $response->headers->set('Cache-Control', 'public, max-age=604800');
            }
        }

        return $response;
    }
}
