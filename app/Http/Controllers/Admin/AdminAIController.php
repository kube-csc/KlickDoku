<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AIDescriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminAIController extends Controller
{
    public function __construct(protected AIDescriptionService $aiService)
    {
    }

    /**
     * Generiert eine KI-gestützte Beschreibung basierend auf Titel, Typ und Kategorie.
     */
    public function generateDescription(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'type'          => 'required|in:guide,tutorial,page,category,guide_step,tutorial_step',
            'category'      => 'nullable|string|max:255',
            'tags'          => 'nullable|array',
            'tags.*'        => 'string',
            'keywords'      => 'nullable|array',
            'keywords.*'    => 'string|max:100',
            'referenceUrls' => 'nullable|array',
            'referenceUrls.*' => 'string|url|max:2048',
            'customPrompt'  => 'nullable|string|max:500',
        ]);

        $description = $this->aiService->generateDescription(
            $validated['title'],
            $validated['type'],
            $validated['category'] ?? '',
            $validated['tags'] ?? [],
            'de',
            $validated['keywords'] ?? [],
            $validated['referenceUrls'] ?? [],
            $validated['customPrompt'] ?? ''
        );

        return response()->json([
            'description' => $description,
        ]);
    }

    /**
     * Generiert eine KI-gestützte Übersetzung eines Textes.
     */
    public function generateTranslation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'originalText' => 'required|string',
            'sourceLocale' => 'required|string|max:10',
            'targetLocale' => 'required|string|max:10',
            'context'      => 'nullable|string|max:500',
        ]);

        $translation = $this->aiService->generateTranslation(
            $validated['originalText'],
            $validated['sourceLocale'],
            $validated['targetLocale'],
            $validated['context'] ?? ''
        );

        return response()->json([
            'translation' => $translation,
        ]);
    }

    /**
     * Generiert einen KI-gestützten Inhaltstext (vollständiger Anleitungsentwurf).
     */
    public function generateContent(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'type'          => 'required|in:guide,tutorial,page',
            'category'      => 'nullable|string|max:255',
            'tags'          => 'nullable|array',
            'tags.*'        => 'string',
            'steps'         => 'nullable|array',
            'steps.*.title' => 'nullable|string|max:255',
            'keywords'      => 'nullable|array',
            'keywords.*'    => 'string|max:100',
            'referenceUrls' => 'nullable|array',
            'referenceUrls.*' => 'string|url|max:2048',
            'customPrompt'  => 'nullable|string|max:500',
        ]);

        $steps = [];
        if (!empty($validated['steps'])) {
            foreach ($validated['steps'] as $step) {
                $steps[] = ['title' => $step['title'] ?? ''];
            }
        }

        $content = $this->aiService->generateContent(
            $validated['title'],
            $validated['type'],
            $validated['category'] ?? '',
            $validated['tags'] ?? [],
            $steps,
            'de',
            $validated['keywords'] ?? [],
            $validated['referenceUrls'] ?? [],
            $validated['customPrompt'] ?? ''
        );

        return response()->json([
            'content' => $content,
        ]);
    }
}
