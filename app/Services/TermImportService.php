<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectTerm;
use Illuminate\Support\Facades\File;

class TermImportService
{
    /**
     * Importiert Begriffe aus Laravel Language Files als Vorschlag.
     *
     * @param string $path Pfad zum Language-Verzeichnis (z.B. lang/de)
     * @return array Array von [placeholder_key => ['value_de' => ..., 'value_en' => ...]]
     */
    public function importFromLanguageFiles(string $path): array
    {
        $terms = [];

        if (!File::isDirectory($path)) {
            return $terms;
        }

        // navigation.php und common.php durchsuchen
        $files = ['navigation.php', 'common.php'];
        foreach ($files as $file) {
            $filePath = $path . DIRECTORY_SEPARATOR . $file;
            if (!File::exists($filePath)) {
                continue;
            }

            $data = require $filePath;
            $this->extractTerms($data, $terms);
        }

        return $terms;
    }

    /**
     * Rekursive Extraktion von Begriffen aus einem Array.
     */
    protected function extractTerms(array $data, array &$terms, string $prefix = ''): void
    {
        foreach ($data as $key => $value) {
            $fullKey = $prefix ? "{$prefix}_{$key}" : $key;

            if (is_array($value)) {
                $this->extractTerms($value, $terms, $fullKey);
            } else {
                // Nur sinnvolle Werte übernehmen (keine leeren Strings)
                if (is_string($value) && strlen(trim($value)) > 0) {
                    $terms[$fullKey] = $value;
                }
            }
        }
    }

    /**
     * Importiert Begriffe von einer API.
     *
     * @param string $url Die API-URL
     * @param string|null $token Optionaler Auth-Token
     * @return array Array von [placeholder_key => value]
     */
    public function importFromApi(string $url, ?string $token = null): array
    {
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        if ($token) {
            $headers['Authorization'] = 'Bearer ' . $token;
        }

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders($headers)->get($url);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            // Fehler protokollieren, aber nicht werfen
            \Log::warning('TermImportService: API-Import fehlgeschlagen', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
        }

        return [];
    }

    /**
     * Gibt Vorschläge für Begriffe eines Projekts zurück.
     * Kombiniert Standard-Platzhalter mit vorhandenen Terms.
     *
     * @param Project $project Das Projekt
     * @return array Array von Vorschlägen
     */
    public function suggestTerms(Project $project): array
    {
        // Bestehende Terms laden
        $existingTerms = $project->terms->keyBy('placeholder_key');

        // Standard-Platzhalter aus PlaceholderDefinition laden
        $definitions = \App\Models\PlaceholderDefinition::all();

        $suggestions = [];
        foreach ($definitions as $definition) {
            $existing = $existingTerms->get($definition->key);
            $suggestions[] = [
                'placeholder_key' => $definition->key,
                'value_de' => $existing ? $existing->value_de : $definition->default_value_de,
                'value_en' => $existing ? $existing->value_en : $definition->default_value_en,
                'description_de' => $definition->description_de,
                'description_en' => $definition->description_en,
                'category' => $definition->category,
                'is_set' => $existing !== null,
            ];
        }

        return $suggestions;
    }

    /**
     * Wendet importierte Begriffe auf ein Projekt an.
     *
     * @param Project $project Das Projekt
     * @param array $terms Array von [placeholder_key => ['value_de' => ..., 'value_en' => ...]]
     * @param bool $overwrite Bestehende Terms überschreiben
     * @return int Anzahl der erstellten/aktualisierten Terms
     */
    public function applyTerms(Project $project, array $terms, bool $overwrite = false): int
    {
        $count = 0;
        foreach ($terms as $key => $value) {
            $existing = $project->terms->firstWhere('placeholder_key', $key);

            if ($existing && ! $overwrite) {
                continue;
            }

            $termData = is_array($value)
                ? $value
                : ['value_de' => $value];

            if ($existing) {
                $existing->update($termData);
            } else {
                $project->terms()->create(array_merge([
                    'placeholder_key' => $key,
                ], $termData));
            }

            $count++;
        }

        // Cache invalidieren
        app(PlaceholderService::class)->clearProjectCache($project);

        return $count;
    }
}
