<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProjectResolver
{
    /**
     * Liste der reservierten Subdomains, die nicht als Projekt interpretiert werden.
     */
    protected array $reservedSubdomains = [
        'www',
        'api',
        'admin',
        'mail',
        'ftp',
        'smtp',
        'pop',
        'imap',
        'webmail',
        'cdn',
        'static',
    ];

    /**
     * Aktuelles Projekt aus der Anfrage ermitteln.
     * Reihenfolge: Subdomain → Query-Parameter → Domain → null (Master)
     *
     * @param Request $request Die aktuelle HTTP-Anfrage
     * @return Project|null Das erkannte Projekt oder null
     */
    public function resolveFromRequest(Request $request): ?Project
    {
        // 1. Subdomain prüfen
        $project = $this->resolveFromSubdomain($this->getSubdomain($request));
        if ($project) {
            return $project;
        }

        // 2. Query-Parameter prüfen
        $project = $this->resolveFromQueryParam($request);
        if ($project) {
            return $project;
        }

        // 3. Domain prüfen
        $project = $this->resolveFromDomain($request->getHost());
        if ($project) {
            return $project;
        }

        return null;
    }

    /**
     * Projekt aus Subdomain ermitteln.
     *
     * @param string|null $subdomain Die Subdomain
     * @return Project|null
     */
    public function resolveFromSubdomain(?string $subdomain): ?Project
    {
        if (! $subdomain) {
            return null;
        }

        // Reservierte Subdomains ignorieren
        if (in_array(strtolower($subdomain), $this->reservedSubdomains)) {
            return null;
        }

        $cacheKey = "project_by_subdomain_{$subdomain}";

        $project = Cache::get($cacheKey);
        if ($project === null) {
            $project = Project::active()
                ->where('subdomain', strtolower($subdomain))
                ->first();
            // Nur cachen, wenn ein Projekt gefunden wurde (nicht NULL)
            if ($project) {
                Cache::put($cacheKey, $project, 3600);
            }
        }
        return $project;
    }

    /**
     * Projekt aus Query-Parameter ermitteln.
     *
     * @param Request $request Die HTTP-Anfrage
     * @return Project|null
     */
    public function resolveFromQueryParam(Request $request): ?Project
    {
        $projectSlug = $request->get('project');
        if (! $projectSlug) {
            return null;
        }

        $cacheKey = "project_by_slug_{$projectSlug}";

        $project = Cache::get($cacheKey);
        if ($project === null) {
            $project = Project::active()
                ->where('slug', $projectSlug)
                ->first();
            // Nur cachen, wenn ein Projekt gefunden wurde
            if ($project) {
                Cache::put($cacheKey, $project, 3600);
            }
        }
        return $project;
    }

    /**
     * Projekt aus Domain ermitteln.
     *
     * @param string $domain Die Domain
     * @return Project|null
     */
    public function resolveFromDomain(string $domain): ?Project
    {
        $cacheKey = "project_by_domain_{$domain}";

        $project = Cache::get($cacheKey);
        if ($project === null) {
            $project = Project::active()
                ->where('domain', $domain)
                ->first();
            // Nur cachen, wenn ein Projekt gefunden wurde
            if ($project) {
                Cache::put($cacheKey, $project, 3600);
            }
        }
        return $project;
    }

    /**
     * Subdomain aus der Anfrage extrahieren.
     *
     * @param Request $request
     * @return string|null
     */
    protected function getSubdomain(Request $request): ?string
    {
        $host = $request->getHost();

        // IP-Adressen haben keine Subdomains (z.B. 127.0.0.1, 192.168.1.1)
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return null;
        }

        $parts = explode('.', $host);

        // Mindestens 3 Teile für eine Subdomain (z.B. projekt.klickdoku.de)
        if (count($parts) < 3) {
            return null;
        }

        return $parts[0];
    }

    /**
     * Cache für alle Projekt-Auflösungen invalidieren.
     * Da Cache-Tags bei Datei-basiertem Cache nicht unterstützt werden,
     * wird der gesamte Cache geleert als Fallback.
     */
    public function clearCache(): void
    {
        // Leere den gesamten Application-Cache
        // Dies ist der zuverlässigste Ansatz für Datei-basierten Cache
        Cache::flush();
    }

    /**
     * Statische Methode für den Helper currentProject().
     *
     * @return Project|null
     */
    public static function current(): ?Project
    {
        return request()->attributes->get('project');
    }
}
