<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidHotspotLink implements ValidationRule
{
    /**
     * Validiert, dass der Hotspot-Link eine gültige URL oder ein relativer Pfad ist.
     *
     * Akzeptiert:
     * - Absolute URLs: https://www.ssv-datteln.de/login, http://example.com/seite
     * - Relative Pfade: /login, /mitglieder/suche, ?status=offen
     * - Platzhalter-URLs: https://example.de/{{events}}, /{{dashboard}}
     * - Leere Werte (nullable — kein Link erforderlich)
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Leere Werte sind erlaubt (nullable)
        if (empty($value)) {
            return;
        }

        // Absolute URL (http:// oder https://)
        if (preg_match('~^https?://[^\s]+~i', $value)) {
            return;
        }

        // Relativer Pfad (/...) oder Query-Parameter (?...)
        if (preg_match('~^[/?#][^\s]*~', $value)) {
            return;
        }

        // Protokoll-unabhängige URL (//example.com/...)
        if (preg_match('~^//[^\s]+~', $value)) {
            return;
        }

        $fail('Der Hotspot-Link muss eine gültige URL sein (z. B. https://example.de/seite) oder ein relativer Pfad (z. B. /login).');
    }
}
