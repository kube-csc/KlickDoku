<?php

use App\Models\Project;
use App\Services\PlaceholderService;
use App\Services\ProjectResolver;

if (!function_exists('currentProject')) {
    /**
     * Gibt das aktuelle Projekt zurück (aus der Request).
     *
     * @return Project|null
     */
    function currentProject(): ?Project
    {
        return request()->attributes->get('project');
    }
}

if (!function_exists('replacePlaceholders')) {
    /**
     * Ersetzt Platzhalter in einem Text durch projektspezifische Werte.
     *
     * @param string|null $text
     * @param string|null $locale
     * @return string
     */
    function replacePlaceholders(?string $text, ?string $locale = null): string
    {
        $project = currentProject();
        if (!$project) {
            return $text ?? '';
        }

        return app(PlaceholderService::class)->resolve($text, $project, $locale);
    }
}

if (!function_exists('slugFromTitle')) {
    /**
     * Generiert einen sauberen Slug aus einem Titel, der Platzhalter enthalten kann.
     * Entfernt alle {{placeholder}}-Muster vor der Slug-Generierung, damit der Slug
     * stabil und unabhängig vom aktuellen Projekt bleibt.
     *
     * Beispiel: "So verwalten Sie Ihre {{events}}" → "so-verwalten-sie-ihre"
     *
     * @param string $title
     * @return string
     */
    function slugFromTitle(string $title): string
    {
        // Entferne alle {{...}} Platzhalter aus dem Titel
        $cleanTitle = preg_replace('/\{\{[^}]+\}\}/', '', $title);
        // Entferne mehrfache Leerzeichen, die durch das Entfernen entstehen
        $cleanTitle = preg_replace('/\s+/', '', trim($cleanTitle));
        return \Illuminate\Support\Str::slug($cleanTitle);
    }
}
