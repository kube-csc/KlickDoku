<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

/**
 * Service zum Generieren von Beschreibungstexten.
 *
 * Unterstützt drei Provider:
 * - "mock": Vordefinierte Vorlagen (Standard, keine API nötig)
 * - "openai": OpenAI GPT-4o-mini (benötigt OPENAI_API_KEY)
 * - "groq": Groq Llama 3 (benötigt GROQ_API_KEY)
 *
 * GitHub Copilot wird NICHT unterstützt:
 * Copilot hat keine öffentliche API für Textgenerierung und kein PHP-SDK.
 * Es funktioniert nur als IDE-Plugin (PhpStorm, VS Code etc.).
 *
 * Umstellung auf echte KI in der .env:
 *   AI_PROVIDER=openai    (oder "groq")
 *   OPENAI_API_KEY=sk-...
 *   GROQ_API_KEY=gsk_...
 */
class AIDescriptionService
{
    /**
     * Generiert eine Kurzbeschreibung basierend auf den gegebenen Parametern.
     */
    public function generateDescription(
        string $title,
        string $type,
        string $category = '',
        array $tags = [],
        string $locale = 'de',
        array $keywords = [],
        array $referenceUrls = [],
        string $customPrompt = ''
    ): string {
        $provider = config('services.ai.provider', 'mock');

        return match ($provider) {
            'openai' => $this->generateWithOpenAI($title, $type, $category, $tags, $locale, $keywords, $referenceUrls, $customPrompt),
            'groq'   => $this->generateWithGroq($title, $type, $category, $tags, $locale, $keywords, $referenceUrls, $customPrompt),
            default  => $this->generateMockDescription($title, $type, $category, $tags, $keywords, $referenceUrls, $customPrompt),
        };
    }

    /**
     * Baut den Prompt für die KI-API zusammen.
     */
    protected function buildPrompt(
        string $title,
        string $type,
        string $category,
        array $tags,
        string $locale,
        array $keywords,
        array $referenceUrls,
        string $customPrompt
    ): string {
        $typeLabels = [
            'guide' => 'Anleitung',
            'tutorial' => 'Tutorial',
            'page' => 'Seite',
            'category' => 'Kategorie',
            'guide_step' => 'Anleitungsschritt',
            'tutorial_step' => 'Tutorial-Schritt',
        ];
        $typeLabel = $typeLabels[$type] ?? 'Dokument';

        $prompt = "Erstelle eine kurze, prägnante Beschreibung (max. 200 Zeichen) für eine {$typeLabel}.\n\n";
        $prompt .= "Titel: {$title}\n";

        if ($category) {
            $prompt .= "Kontext: {$category}\n";
        }

        if (!empty($tags)) {
            $prompt .= "Tags: " . implode(', ', $tags) . "\n";
        }

        if (!empty($keywords)) {
            $prompt .= "Schlüsselbegriffe: " . implode(', ', $keywords) . "\n";
        }

        if (!empty($referenceUrls)) {
            $prompt .= "Referenz-URLs: " . implode(', ', $referenceUrls) . "\n";
        }

        if ($customPrompt) {
            $prompt .= "\nZusätzliche Anweisung: {$customPrompt}\n";
        }

        $prompt .= "\nSprache: " . ucfirst($locale) . "\n";
        $prompt .= "Zielgruppe: Vereinsmitglieder mit technischem Hintergrund\n";
        $prompt .= "Gib NUR die Beschreibung zurück, ohne Erklärungen oder Formatierung.";

        return $prompt;
    }

    /**
     * Generiert Beschreibung mit OpenAI API.
     */
    protected function generateWithOpenAI(
        string $title,
        string $type,
        string $category,
        array $tags,
        string $locale,
        array $keywords,
        array $referenceUrls,
        string $customPrompt
    ): string {
        $config = config('services.ai.openai');
        $apiKey = $config['api_key'] ?? '';

        if (empty($apiKey)) {
            return $this->generateMockDescription($title, $type, $category, $tags, $keywords, $referenceUrls, $customPrompt);
        }

        $prompt = $this->buildPrompt($title, $type, $category, $tags, $locale, $keywords, $referenceUrls, $customPrompt);

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type'  => 'application/json',
            ])->post($config['url'] ?? 'https://api.openai.com/v1/chat/completions', [
                'model'    => $config['model'] ?? 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => 'Du bist ein Assistent, der kurze Beschreibungen für Anleitungen und Tutorials erstellt.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'max_tokens' => 250,
                'temperature' => 0.7,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['choices'][0]['message']['content'] ?? '';
                return mb_substr(trim($content), 0, 200);
            }

            // Fallback bei Fehler
            return $this->generateMockDescription($title, $type, $category, $tags, $keywords, $referenceUrls, $customPrompt);
        } catch (\Exception $e) {
            // Bei Exception auf Mock zurückfallen
            return $this->generateMockDescription($title, $type, $category, $tags, $keywords, $referenceUrls, $customPrompt);
        }
    }

    /**
     * Generiert Beschreibung mit Groq API (OpenAI-kompatibles Format).
     */
    protected function generateWithGroq(
        string $title,
        string $type,
        string $category,
        array $tags,
        string $locale,
        array $keywords,
        array $referenceUrls,
        string $customPrompt
    ): string {
        $config = config('services.ai.groq');
        $apiKey = $config['api_key'] ?? '';

        if (empty($apiKey)) {
            return $this->generateMockDescription($title, $type, $category, $tags, $keywords, $referenceUrls, $customPrompt);
        }

        $prompt = $this->buildPrompt($title, $type, $category, $tags, $locale, $keywords, $referenceUrls, $customPrompt);

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type'  => 'application/json',
            ])->post($config['url'] ?? 'https://api.groq.com/openai/v1/chat/completions', [
                'model'    => $config['model'] ?? 'llama-3.3-70b-versatile',
                'messages' => [
                    ['role' => 'system', 'content' => 'Du bist ein Assistent, der kurze Beschreibungen für Anleitungen und Tutorials erstellt.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'max_tokens' => 250,
                'temperature' => 0.7,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['choices'][0]['message']['content'] ?? '';
                return mb_substr(trim($content), 0, 200);
            }

            // Fallback bei Fehler
            return $this->generateMockDescription($title, $type, $category, $tags, $keywords, $referenceUrls, $customPrompt);
        } catch (\Exception $e) {
            // Bei Exception auf Mock zurückfallen
            return $this->generateMockDescription($title, $type, $category, $tags, $keywords, $referenceUrls, $customPrompt);
        }
    }

    /**
     * Mock-Implementierung: generiert eine plausible Beschreibung aus Vorlagen.
     */
    protected function generateMockDescription(
        string $title,
        string $type,
        string $category,
        array $tags,
        array $keywords = [],
        array $referenceUrls = [],
        string $customPrompt = ''
    ): string {
        $typeLabels = [
            'guide' => 'Anleitung',
            'tutorial' => 'Tutorial',
            'page' => 'Seite',
            'category' => 'Kategorie',
            'guide_step' => 'Anleitungsschritt',
            'tutorial_step' => 'Tutorial-Schritt',
        ];

        $typeLabel = $typeLabels[$type] ?? 'Dokument';

        $templates = [
            "Diese {type} zeigt Ihnen Schritt für Schritt, wie Sie \"{$title}\" erfolgreich umsetzen. Perfekt für Einsteiger und als schnelle Referenz.",
            "Lernen Sie in dieser {type}, wie \"{$title}\" funktioniert. Klare Erklärungen und praktische Tipps für den Alltag.",
            "Eine praxisnahe {type} zum Thema \"{$title}\". Mit konkreten Beispielen und bewährten Vorgehensweisen.",
            "\"{$title}\" einfach erklärt: Diese {type} führt Sie durch alle wichtigen Schritte und gibt wertvolle Hinweise.",
            "Komplette {type} zu \"{$title}\" – von der Vorbereitung bis zur Umsetzung. Ideal für Vereinsmitglieder.",
        ];

        // Kategorie hinzufügen, wenn vorhanden
        if ($category) {
            $templates[] = "Kategorien-spezifische {type} aus dem Bereich \"{$category}\": \"{$title}\" detailliert und verständlich erklärt.";
            $templates[] = "Praktische {type} aus der Kategorie \"{$category}\": So gehen Sie bei \"{$title}\" richtig vor.";
        }

        // Tags hinzufügen, wenn vorhanden
        if (!empty($tags)) {
            $tagString = implode(', ', array_slice($tags, 0, 3));
            $templates[] = "Diese {type} behandelt \"{$title}\" mit Fokus auf {$tagString}. Schritt-für-Schritt-Anleitung mit Tipps und Tricks.";
        }

        // Schlüsselbegriffe einbetten, wenn vorhanden
        if (!empty($keywords)) {
            $keywordString = implode(', ', array_slice($keywords, 0, 3));
            $templates[] = "Diese {type} zu \"{$title}\" behandelt {$keywordString} und mehr. Praxisnah erklärt mit Beispielen.";
            $templates[] = "\"{$title}\" im Detail: {$keywordString} und weitere Themen werden in dieser {type} verständlich erklärt.";
        }

        // Referenz-URLs als Hinweis einbetten, wenn vorhanden
        if (!empty($referenceUrls)) {
            $urlCount = count($referenceUrls);
            $templates[] = "Diese {type} zu \"{$title}\" basiert auf {$urlCount} Quellen. Detaillierte Erklärungen mit weiterführenden Links.";
        }

        // Kombinierte Vorlage wenn Keywords UND URLs vorhanden
        if (!empty($keywords) && !empty($referenceUrls)) {
            $keywordString = implode(', ', array_slice($keywords, 0, 2));
            $templates[] = "\"{$title}\" mit Fokus auf {$keywordString}. Diese {type} liefert praxisnahe Erklärungen und weiterführende Quellen.";
        }

        // Benutzerdefinierter Prompt — wenn vorhanden, als eigene Vorlage nutzen
        if (!empty(trim($customPrompt))) {
            $trimmedPrompt = trim($customPrompt);
            $templates[] = "{$trimmedPrompt} Diese {type} zu \"{$title}\" liefert genau das, was Sie brauchen.";
        }

        // Deterministische Auswahl basierend auf Titel-Länge (wiederholbar)
        $contextHash = strlen($title) + count($keywords) + count($referenceUrls) + strlen($customPrompt);
        $index = $contextHash % count($templates);

        $description = $templates[$index];
        $description = str_replace('{type}', $typeLabel, $description);

        // Auf max. 200 Zeichen kürzen (mb_strlen für Unicode-korrekte Zählung)
        if (mb_strlen($description) > 200) {
            $description = mb_substr($description, 0, 197) . '...';
        }

        return $description;
    }

    /**
     * Generiert eine Übersetzung eines Textes in die Zielsprache.
     */
    public function generateTranslation(
        string $originalText,
        string $sourceLocale,
        string $targetLocale,
        string $context = ''
    ): string {
        $provider = config('services.ai.provider', 'mock');

        return match ($provider) {
            'openai' => $this->translateWithOpenAI($originalText, $sourceLocale, $targetLocale, $context),
            'groq'   => $this->translateWithGroq($originalText, $sourceLocale, $targetLocale, $context),
            default  => $this->translateMock($originalText, $sourceLocale, $targetLocale, $context),
        };
    }

    /**
     * Übersetzung mit OpenAI API.
     */
    protected function translateWithOpenAI(
        string $originalText,
        string $sourceLocale,
        string $targetLocale,
        string $context
    ): string {
        $config = config('services.ai.openai');
        $apiKey = $config['api_key'] ?? '';

        if (empty($apiKey)) {
            return $this->translateMock($originalText, $sourceLocale, $targetLocale, $context);
        }

        $sourceLang = $this->getLocaleName($sourceLocale);
        $targetLang = $this->getLocaleName($targetLocale);

        $prompt = "Übersetze den folgenden Text von {$sourceLang} nach {$targetLang}.\n\n";
        $prompt .= "Originaltext: {$originalText}\n";
        if ($context) {
            $prompt .= "Kontext: {$context}\n";
        }
        $prompt .= "Gib NUR die Übersetzung zurück, ohne Erklärungen oder Formatierung.";

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type'  => 'application/json',
            ])->post($config['url'] ?? 'https://api.openai.com/v1/chat/completions', [
                'model'    => $config['model'] ?? 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => 'Du bist ein professioneller Übersetzer.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'max_tokens' => 500,
                'temperature' => 0.3,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['choices'][0]['message']['content'] ?? '';
                return trim($content);
            }

            return $this->translateMock($originalText, $sourceLocale, $targetLocale, $context);
        } catch (\Exception $e) {
            return $this->translateMock($originalText, $sourceLocale, $targetLocale, $context);
        }
    }

    /**
     * Übersetzung mit Groq API.
     */
    protected function translateWithGroq(
        string $originalText,
        string $sourceLocale,
        string $targetLocale,
        string $context
    ): string {
        $config = config('services.ai.groq');
        $apiKey = $config['api_key'] ?? '';

        if (empty($apiKey)) {
            return $this->translateMock($originalText, $sourceLocale, $targetLocale, $context);
        }

        $sourceLang = $this->getLocaleName($sourceLocale);
        $targetLang = $this->getLocaleName($targetLocale);

        $prompt = "Übersetze den folgenden Text von {$sourceLang} nach {$targetLang}.\n\n";
        $prompt .= "Originaltext: {$originalText}\n";
        if ($context) {
            $prompt .= "Kontext: {$context}\n";
        }
        $prompt .= "Gib NUR die Übersetzung zurück, ohne Erklärungen oder Formatierung.";

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type'  => 'application/json',
            ])->post($config['url'] ?? 'https://api.groq.com/openai/v1/chat/completions', [
                'model'    => $config['model'] ?? 'llama-3.3-70b-versatile',
                'messages' => [
                    ['role' => 'system', 'content' => 'Du bist ein professioneller Übersetzer.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'max_tokens' => 500,
                'temperature' => 0.3,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['choices'][0]['message']['content'] ?? '';
                return trim($content);
            }

            return $this->translateMock($originalText, $sourceLocale, $targetLocale, $context);
        } catch (\Exception $e) {
            return $this->translateMock($originalText, $sourceLocale, $targetLocale, $context);
        }
    }

    /**
     * Mock-Übersetzung: gibt den Originaltext mit Hinweis zurück.
     */
    protected function translateMock(
        string $originalText,
        string $sourceLocale,
        string $targetLocale,
        string $context
    ): string {
        $targetLang = $this->getLocaleName($targetLocale);
        
        // Mock: gibt den Originaltext zurück mit Hinweis, dass dies ein Mock ist
        // In einer echten Implementierung würde hier eine Übersetzung erfolgen
        return "[{$targetLang} Mock] {$originalText}";
    }

    /**
     * Gibt den Sprachnamen für ein Locale zurück.
     */
    protected function getLocaleName(string $locale): string
    {
        $names = [
            'de' => 'Deutsch',
            'en' => 'Englisch',
            'fr' => 'Französisch',
            'es' => 'Spanisch',
            'it' => 'Italienisch',
            'nl' => 'Niederländisch',
        ];
        return $names[$locale] ?? ucfirst($locale);
    }

    /**
     * Generiert einen längeren Inhaltstext (vollständiger Anleitungsentwurf).
     */
    public function generateContent(
        string $title,
        string $type,
        string $category = '',
        array $tags = [],
        array $steps = [],
        string $locale = 'de',
        array $keywords = [],
        array $referenceUrls = [],
        string $customPrompt = ''
    ): string {
        $provider = config('services.ai.provider', 'mock');

        return match ($provider) {
            'openai' => $this->generateContentWithOpenAI($title, $type, $category, $tags, $steps, $locale, $keywords, $referenceUrls, $customPrompt),
            'groq'   => $this->generateContentWithGroq($title, $type, $category, $tags, $steps, $locale, $keywords, $referenceUrls, $customPrompt),
            default  => $this->generateContentMock($title, $type, $category, $tags, $steps, $keywords, $referenceUrls, $customPrompt),
        };
    }

    /**
     * Baut den Prompt für die Inhaltsgenerierung zusammen.
     */
    protected function buildContentPrompt(
        string $title,
        string $type,
        string $category,
        array $tags,
        array $steps,
        string $locale,
        array $keywords,
        array $referenceUrls,
        string $customPrompt
    ): string {
        $typeLabels = [
            'guide' => 'Anleitung',
            'tutorial' => 'Tutorial',
            'page' => 'Seite',
        ];
        $typeLabel = $typeLabels[$type] ?? 'Dokument';

        $prompt = "Erstelle einen ausführlichen Inhaltstext für eine {$typeLabel}.\n\n";
        $prompt .= "Titel: {$title}\n";

        if ($category) {
            $prompt .= "Kategorie: {$category}\n";
        }

        if (!empty($tags)) {
            $prompt .= "Tags: " . implode(', ', $tags) . "\n";
        }

        if (!empty($steps)) {
            $prompt .= "\nSchritte der {$typeLabel}:\n";
            foreach ($steps as $index => $step) {
                $stepTitle = $step['title'] ?? "Schritt " . ($index + 1);
                $prompt .= "  " . ($index + 1) . ". {$stepTitle}\n";
            }
        }

        if (!empty($keywords)) {
            $prompt .= "\nSchlüsselbegriffe: " . implode(', ', $keywords) . "\n";
        }

        if (!empty($referenceUrls)) {
            $prompt .= "\nReferenz-URLs: " . implode(', ', $referenceUrls) . "\n";
        }

        if ($customPrompt) {
            $prompt .= "\nZusätzliche Anweisung: {$customPrompt}\n";
        }

        $prompt .= "\nStruktur des Inhalts:\n";
        $prompt .= "1. Einleitung: Kurze Einführung zum Thema und warum es wichtig ist\n";
        $prompt .= "2. Hauptteil: Ausführliche Erklärung der Schritte und Hintergründe\n";
        $prompt .= "3. Abschluss: Zusammenfassung und weitere Tipps\n\n";
        $prompt .= "Sprache: " . ucfirst($locale) . "\n";
        $prompt .= "Zielgruppe: Vereinsmitglieder mit technischem Hintergrund\n";
        $prompt .= "Nutze Absätze und Abschnitte für gute Lesbarkeit.\n";
        $prompt .= "Gib NUR den Inhalt zurück, ohne Erklärungen oder Formatierungshinweise.";

        return $prompt;
    }

    /**
     * Generiert Inhalt mit OpenAI API.
     */
    protected function generateContentWithOpenAI(
        string $title,
        string $type,
        string $category,
        array $tags,
        array $steps,
        string $locale,
        array $keywords,
        array $referenceUrls,
        string $customPrompt
    ): string {
        $config = config('services.ai.openai');
        $apiKey = $config['api_key'] ?? '';

        if (empty($apiKey)) {
            return $this->generateContentMock($title, $type, $category, $tags, $steps, $keywords, $referenceUrls, $customPrompt);
        }

        $prompt = $this->buildContentPrompt($title, $type, $category, $tags, $steps, $locale, $keywords, $referenceUrls, $customPrompt);

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type'  => 'application/json',
            ])->post($config['url'] ?? 'https://api.openai.com/v1/chat/completions', [
                'model'    => $config['model'] ?? 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => 'Du bist ein erfahrener Technischer Schriftsteller, der klare und verständliche Anleitungen erstellt.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'max_tokens' => 4000,
                'temperature' => 0.7,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['choices'][0]['message']['content'] ?? '';
                return trim($content);
            }

            return $this->generateContentMock($title, $type, $category, $tags, $steps, $keywords, $referenceUrls, $customPrompt);
        } catch (\Exception $e) {
            return $this->generateContentMock($title, $type, $category, $tags, $steps, $keywords, $referenceUrls, $customPrompt);
        }
    }

    /**
     * Generiert Inhalt mit Groq API.
     */
    protected function generateContentWithGroq(
        string $title,
        string $type,
        string $category,
        array $tags,
        array $steps,
        string $locale,
        array $keywords,
        array $referenceUrls,
        string $customPrompt
    ): string {
        $config = config('services.ai.groq');
        $apiKey = $config['api_key'] ?? '';

        if (empty($apiKey)) {
            return $this->generateContentMock($title, $type, $category, $tags, $steps, $keywords, $referenceUrls, $customPrompt);
        }

        $prompt = $this->buildContentPrompt($title, $type, $category, $tags, $steps, $locale, $keywords, $referenceUrls, $customPrompt);

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type'  => 'application/json',
            ])->post($config['url'] ?? 'https://api.groq.com/openai/v1/chat/completions', [
                'model'    => $config['model'] ?? 'llama-3.3-70b-versatile',
                'messages' => [
                    ['role' => 'system', 'content' => 'Du bist ein erfahrener Technischer Schriftsteller, der klare und verständliche Anleitungen erstellt.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'max_tokens' => 4000,
                'temperature' => 0.7,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['choices'][0]['message']['content'] ?? '';
                return trim($content);
            }

            return $this->generateContentMock($title, $type, $category, $tags, $steps, $keywords, $referenceUrls, $customPrompt);
        } catch (\Exception $e) {
            return $this->generateContentMock($title, $type, $category, $tags, $steps, $keywords, $referenceUrls, $customPrompt);
        }
    }

    /**
     * Mock-Inhaltsgenerierung: erstellt einen plausiblen Entwurf aus Vorlagen.
     */
    protected function generateContentMock(
        string $title,
        string $type,
        string $category,
        array $tags,
        array $steps,
        array $keywords,
        array $referenceUrls,
        string $customPrompt
    ): string {
        $typeLabels = [
            'guide' => 'Anleitung',
            'tutorial' => 'Tutorial',
            'page' => 'Seite',
        ];
        $typeLabel = $typeLabels[$type] ?? 'Dokument';

        $content = "## Einleitung\n\n";
        $content .= "In dieser {$typeLabel} erfahren Sie, wie Sie \"{$title}\" erfolgreich umsetzen. ";
        if ($category) {
            $content .= "Dieses Thema gehört zum Bereich {$category} und ist besonders relevant für Vereinsmitglieder mit technischem Hintergrund.\n\n";
        } else {
            $content .= "Dieses Thema ist besonders relevant für Vereinsmitglieder mit technischem Hintergrund.\n\n";
        }

        if (!empty($steps)) {
            $content .= "## Ablauf\n\n";
            foreach ($steps as $index => $step) {
                $stepTitle = $step['title'] ?? "Schritt " . ($index + 1);
                $content .= "### {$stepTitle}\n\n";
                $content .= "Hier führen Sie den Schritt „{$stepTitle}\" durch. ";
                $content .= "Achten Sie darauf, alle notwendigen Vorbereitungen zu treffen, bevor Sie fortfahren.\n\n";
            }
        }

        if (!empty($keywords)) {
            $content .= "## Wichtige Begriffe\n\n";
            $content .= "Bei der Umsetzung spielen folgende Begriffe eine Rolle: " . implode(', ', $keywords) . ".\n\n";
        }

        $content .= "## Zusammenfassung\n\n";
        $content .= "Sie haben nun die {$typeLabel} \"{$title}\" abgeschlossen. ";
        $content .= "Bei Fragen oder Problemen wenden Sie sich bitte an den Verantwortlichen für diesen Bereich.";

        return $content;
    }
}
