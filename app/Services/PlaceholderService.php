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
}
