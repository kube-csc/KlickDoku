<?php

namespace App\Services;

use App\Models\Project;
use App\Models\PlaceholderDefinition;
use Illuminate\Support\Facades\Cache;

class PlaceholderService
{
    /**
     * Ersetzt alle Platzhalter in einem Text durch projektspezifische Werte.
     *
     * @param string $text Der Text mit Platzhaltern (z.B. "{{events}}")
     * @param Project|null $project Das aktuelle Projekt
     * @param string|null $locale Die Sprache (optional)
     * @return string Der Text mit ersetzten Platzhaltern
     */
    public function resolve(?string $text, ?Project $project = null, ?string $locale = null): string
    {
        if (is_null($text)) {
            return '';
        }

        $locale = $locale ?? app()->getLocale();

        // Keine Ersetzung ohne Projekt
        if (! $project) {
            return $text;
        }

        // Terms aus Cache laden
        $terms = $this->getProjectTerms($project, $locale);

        // Alle {{placeholder}} Muster finden und ersetzen
        $pattern = '/\{\{(\w+)\}\}/';
        $replacement = function (array $m) use ($terms) {
            $key = $m[1];
            return $terms[$key] ?? $m[0]; // Fallback: Original-Platzhalter belassen
        };

        return preg_replace_callback($pattern, $replacement, $text);
    }

    /**
     * Ersetzt Platzhalter in einem Array von Werten.
     *
     * @param array $data Array mit Textfeldern
     * @param Project|null $project Das aktuelle Projekt
     * @param string|null $locale Die Sprache
     * @return array Array mit ersetzten Werten
     */
    public function resolveArray(array $data, ?Project $project = null, ?string $locale = null): array
    {
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $data[$key] = $this->resolve($value, $project, $locale);
            } elseif (is_array($value)) {
                $data[$key] = $this->resolveArray($value, $project, $locale);
            }
        }

        return $data;
    }

    /**
     * Gibt alle definierten Platzhalter zurück.
     *
     * @return array Array von Platzhalter-Definitionen
     */
    public function getAllPlaceholders(): array
    {
        return Cache::remember('placeholder_definitions', 3600, function () {
            return PlaceholderDefinition::all()->keyBy('key')->toArray();
        });
    }

    /**
     * Gibt alle Terms eines Projekts als Array zurück.
     *
     * @param Project $project Das Projekt
     * @param string|null $locale Die Sprache
     * @return array Array [placeholder_key => value]
     */
    public function getProjectTerms(Project $project, ?string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();
        $cacheKey = "project_terms_{$project->slug}_{$locale}";

        return Cache::remember($cacheKey, 3600, function () use ($project, $locale) {
            $terms = [];
            $valueAttribute = $locale === 'en' ? 'value_en' : 'value_de';

            foreach ($project->terms as $term) {
                $value = $term->{$valueAttribute} ?? $term->value_de;
                if ($value) {
                    $terms[$term->placeholder_key] = $value;
                }
            }

            // Fallback: app_name und club_name direkt aus dem Projekt
            if (!isset($terms['app_name'])) {
                $terms['app_name'] = $project->app_name;
            }
            if (!isset($terms['club_name'])) {
                $terms['club_name'] = $project->club_name;
            }

            return $terms;
        });
    }

    /**
     * Invalidiert den Cache für ein Projekt.
     */
    public function clearProjectCache(Project $project): void
    {
        Cache::forget("project_terms_{$project->slug}_de");
        Cache::forget("project_terms_{$project->slug}_en");
    }

    /**
     * Erkennt automatisch Werte in einem Text, die mit Platzhalter-Default-Werten übereinstimmen,
     * und wandelt sie in Platzhalter um.
     *
     * Beispiel: "https://example.de/Veranstaltungen" → "https://example.de/{{events}}"
     *
     * @param string $text Der Text mit konkreten Werten
     * @param string|null $locale Die Sprache (optional)
     * @return string Der Text mit Platzhaltern anstelle der erkannten Werte
     */
    public function autoDetectPlaceholders(?string $text, ?string $locale = null): string
    {
        if (is_null($text)) {
            return '';
        }

        $locale = $locale ?? app()->getLocale();

        // Alle Platzhalter-Definitionen laden
        $placeholders = $this->getAllPlaceholders();

        if (empty($placeholders)) {
            return $text;
        }

        // Sammle alle zu ersetzenden Werte mit ihrer Länge
        $replacements = [];
        foreach ($placeholders as $key => $definition) {
            $valueAttribute = $locale === 'en' ? 'default_value_en' : 'default_value_de';
            $defaultValue = $definition[$valueAttribute] ?? $definition['default_value_de'];

            if ($defaultValue && strlen($defaultValue) > 1) {
                $existingPlaceholder = '{{' . $key . '}}';
                // Bereits bestehende Platzhalter nicht doppelt ersetzen
                if (strpos($text, $existingPlaceholder) !== false) {
                    continue;
                }

                $replacements[] = [
                    'value' => $defaultValue,
                    'placeholder' => $existingPlaceholder,
                    'length' => strlen($defaultValue),
                ];
            }
        }

        // Nach Länge sortieren: längste Werte zuerst, um Teilstring-Kollisionen zu vermeiden
        // z.B. "Veranstaltungen" (events) vor "Veranstaltung" (event)
        usort($replacements, function ($a, $b) {
            return $b['length'] <=> $a['length'];
        });

        // Ersetzungen in der richtigen Reihenfolge anwenden
        foreach ($replacements as $replacement) {
            $text = str_replace($replacement['value'], $replacement['placeholder'], $text);
        }

        return $text;
    }

    /**
     * Erkennt automatisch Platzhalter in Hotspot-Links und anderen Textfeldern.
     * Wendet autoDetectPlaceholders auf alle relevanten Felder an.
     *
     * @param array $hotspotData Hotspot-Daten aus dem Request
     * @param string|null $locale Die Sprache (optional)
     * @return array Hotspot-Daten mit automatisch erkannten Platzhaltern
     */
    public function autoDetectHotspotPlaceholders(array $hotspotData, ?string $locale = null): array
    {
        $fieldsToProcess = ['link', 'label', 'description'];

        foreach ($fieldsToProcess as $field) {
            if (isset($hotspotData[$field]) && !empty($hotspotData[$field])) {
                $hotspotData[$field] = $this->autoDetectPlaceholders($hotspotData[$field], $locale);
            }
        }

        return $hotspotData;
    }

    /**
     * Ersetzt Platzhalter in einem Text durch Default-Werte aus der PlaceholderDefinition.
     * Dient dazu, im Admin-Panel die tatsächlichen Werte anzuzeigen statt der Platzhalter.
     *
     * Beispiel: "https://example.de/{{events}}" → "https://example.de/Veranstaltungen"
     *
     * @param string|null $text Der Text mit Platzhaltern
     * @param string|null $locale Die Sprache (optional)
     * @return string Der Text mit Default-Werten anstelle der Platzhalter
     */
    public function resolveWithDefaults(?string $text, ?string $locale = null): string
    {
        if (is_null($text)) {
            return '';
        }

        $locale = $locale ?? app()->getLocale();

        // Alle Platzhalter-Definitionen laden
        $placeholders = $this->getAllPlaceholders();

        if (empty($placeholders)) {
            return $text;
        }

        // Für jeden Platzhalter: {{key}} durch Default-Wert ersetzen
        foreach ($placeholders as $key => $definition) {
            $valueAttribute = $locale === 'en' ? 'default_value_en' : 'default_value_de';
            $defaultValue = $definition[$valueAttribute] ?? $definition['default_value_de'];

            if ($defaultValue) {
                $placeholder = '{{' . $key . '}}';
                $text = str_replace($placeholder, $defaultValue, $text);
            }
        }

        return $text;
    }

    /**
     * Ersetzt Platzhalter in Hotspot-Daten durch Default-Werte.
     *
     * @param array $hotspotData Hotspot-Daten mit Platzhaltern
     * @param string|null $locale Die Sprache (optional)
     * @return array Hotspot-Daten mit Default-Werten
     */
    public function resolveHotspotWithDefaults(array $hotspotData, ?string $locale = null): array
    {
        $fieldsToProcess = ['link', 'label', 'description'];

        foreach ($fieldsToProcess as $field) {
            if (isset($hotspotData[$field]) && !empty($hotspotData[$field])) {
                $hotspotData[$field] = $this->resolveWithDefaults($hotspotData[$field], $locale);
            }
        }

        return $hotspotData;
    }

    /**
     * Schreibt die Domain in einer URL um – von der Quelldomain zur Projekt-Domain.
     *
     * Beispiel: source_domain = "https://www.ssv-datteln.de", project_domain = "https://klickdoc.ssv-datteln.de"
     *   "https://www.ssv-datteln.de/login" → "https://klickdoc.ssv-datteln.de/login"
     *
     * Berücksichtigt http/https Protokoll und Subdomains.
     *
     * @param string|null $url Die URL mit der Quelldomain
     * @param string|null $sourceDomain Die Quelldomain (z.B. "https://www.ssv-datteln.de")
     * @param string|null $targetDomain Die Ziel-Domain (z.B. "https://klickdoc.ssv-datteln.de")
     * @return string Die URL mit umgeschriebener Domain
     */
    public function rewriteDomain(?string $url, ?string $sourceDomain, ?string $targetDomain): string
    {
        if (is_null($url) || empty($url)) {
            return '';
        }

        // Wenn keine Quelldomain konfiguriert, URL unverändert zurückgeben
        if (empty($sourceDomain)) {
            return $url;
        }

        // Wenn keine Ziel-Domain konfiguriert, URL unverändert zurückgeben
        if (empty($targetDomain)) {
            return $url;
        }

        // Normalisiere Quelldomain: entferne trailing slashes
        $sourceNormalized = rtrim($sourceDomain, '/');

        // Prüfen, ob die URL mit der Quelldomain beginnt
        if (stripos($url, $sourceNormalized) !== 0) {
            // Die URL beginnt nicht mit der Quelldomain – nichts zu tun
            return $url;
        }

        // Normalisiere Ziel-Domain: entferne trailing slashes
        $targetNormalized = rtrim($targetDomain, '/');

        // Extrahiere den Pfad aus der URL (alles nach der Quelldomain)
        $path = substr($url, strlen($sourceNormalized));

        // Falls der Pfad leer ist, einen Slash hinzufügen
        if (empty($path)) {
            $path = '/';
        }

        return $targetNormalized . $path;
    }

    /**
     * Wendet Domain-Rewriting auf Hotspot-Links an.
     * Ersetzt die Quelldomain durch die Projekt-Domain in allen Hotspot-Links.
     *
     * @param array $hotspotData Hotspot-Daten
     * @param Project|null $project Das aktuelle Projekt
     * @return array Hotspot-Daten mit umgeschriebenen Domains
     */
    public function rewriteHotspotDomains(array $hotspotData, ?Project $project = null): array
    {
        if (!$project || empty($project->source_domain)) {
            return $hotspotData;
        }

        // Ziel-Domain zusammenbauen aus Subdomain oder Domain des Projekts
        $targetDomain = $this->getProjectTargetDomain($project);

        if (empty($targetDomain)) {
            return $hotspotData;
        }

        if (!empty($hotspotData['link'])) {
            $hotspotData['link'] = $this->rewriteDomain($hotspotData['link'], $project->source_domain, $targetDomain);
        }

        return $hotspotData;
    }

    /**
     * Bestimmt die Ziel-Domain eines Projekts für Domain-Rewriting.
     * Priorität: Subdomain > Domain. Falls keine vollständige URL, wird APP_URL verwendet.
     *
     * @param Project $project Das Projekt
     * @return string|null Die Ziel-Domain als vollständige URL
     */
    public function getProjectTargetDomain(?Project $project = null): ?string
    {
        if (!$project) {
            return null;
        }

        $appUrl = config('app.url', 'http://localhost');

        // Wenn Subdomain gesetzt: subdomain + Hauptdomain aus APP_URL
        if (!empty($project->subdomain)) {
            // Extrahiere Host aus APP_URL
            $parsedAppUrl = parse_url($appUrl);
            $appHost = $parsedAppUrl['host'] ?? 'localhost';
            $appScheme = $parsedAppUrl['scheme'] ?? 'http';

            // Finde die Hauptdomain
            $parts = explode('.', $appHost);

            // Bei IP-Adressen (z.B. 127.0.0.1) den gesamten Host verwenden
            if (filter_var($appHost, FILTER_VALIDATE_IP)) {
                return $appScheme . '://' . $project->subdomain . '.' . $appHost;
            }

            // Bei echten Domains: letzte zwei Teile (z.B. "ssv-datteln.de" aus "www.ssv-datteln.de")
            if (count($parts) >= 3) {
                $mainDomain = implode('.', array_slice($parts, -2));
                return $appScheme . '://' . $project->subdomain . '.' . $mainDomain;
            }

            // Bei einfachen Hosts (z.B. "localhost") den gesamten Host verwenden
            return $appScheme . '://' . $project->subdomain . '.' . $appHost;
        }

        // Wenn Domain gesetzt und eine vollständige URL ist
        if (!empty($project->domain)) {
            $domain = $project->domain;
            // Falls keine Protokoll-Präfix, http:// hinzufügen
            if (!str_starts_with($domain, 'http://') && !str_starts_with($domain, 'https://')) {
                $domain = 'https://' . $domain;
            }
            return $domain;
        }

        return null;
    }
}
