<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Erlaubte Sprachen.
     *
     * ACHTUNG: Bei Hinzufügen einer dritten Sprache (z. B. 'fr' für Französisch)
     * sind nur folgende Schritte erforderlich:
     *   1. Sprachcode hier und in config/app.php (supported_locales) hinzufügen
     *   2. Übersetzungsdateien in lang/{code}/ erstellen (z. B. lang/fr/navigation.php)
     *   3. Übersetzungen in content_translations-Tabelle anlegen
     * Kein weiterer Code-Änderungen notwendig!
     */
    protected array $supportedLocales = ['de', 'en'];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Sprache aus der Route-Parameter {locale} lesen
        $locale = $request->route('locale');

        if ($locale && in_array($locale, $this->supportedLocales)) {
            app()->setLocale($locale);
            session()->put('locale', $locale);
        } elseif (session()->has('locale') && in_array(session('locale'), $this->supportedLocales)) {
            // 2. Fallback: Session
            app()->setLocale(session('locale'));
        } elseif ($request->header('Accept-Language')) {
            // 3. Fallback: Browser-Sprache
            $acceptLang = $request->header('Accept-Language');
            foreach ($this->supportedLocales as $supported) {
                if (str_contains($acceptLang, $supported)) {
                    app()->setLocale($supported);
                    session()->put('locale', $supported);
                    break;
                }
            }
        }

        // 4. Root-URL ohne Sprachpräfix umleiten
        if ($request->path() === '/' && !is_null($request->route())) {
            $defaultLocale = app()->getLocale();
            if (!in_array($defaultLocale, $this->supportedLocales)) {
                $defaultLocale = 'de';
            }
            // Nur umleiten wenn es die welcome-Route ist
            if ($request->route()->getName() === 'home') {
                return redirect()->to("{$defaultLocale}/");
            }
        }

        return $next($request);
    }
}
