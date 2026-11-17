<?php

namespace App\Http\Controllers;

use App\Models\Tutorial;
use App\Models\Category;
use App\Models\TutorialProgress;
use App\Services\PlaceholderService;
use App\Services\ProjectScreenshotService;
use Illuminate\Http\Request;

class TutorialController extends Controller
{
    /**
     * Alle veröffentlichten Tutorials auflisten.
     */
    public function index(Request $request)
    {
        $project = $request->attributes->get('project');
        $query = Tutorial::with(['category', 'category.translations', 'author', 'translations'])
            ->where('status', 'published')
            ->orderBy('created_at', 'desc');

        // Projekt-Filter: nur zugewiesene Tutorials anzeigen
        if ($project) {
            $query->forProject($project);
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $tutorials = $query->paginate(12);
        $categories = Category::whereNull('parent_id')->with('translations')->orderBy('sort_order')->get();

        return view('tutorials.index', compact('tutorials', 'categories'));
    }

    /**
     * Einzelnes Tutorial anzeigen (Player).
     */
    public function show(string $locale, string $slug)
    {
        $project = request()->attributes->get('project');
        $tutorial = Tutorial::where('slug', $slug)->firstOrFail();
        if ($tutorial->status !== 'published') {
            abort(404);
        }

        // Projekt-Zuweisung prüfen
        if ($project && !$tutorial->projects->contains($project)) {
            abort(404);
        }

        $tutorial->increment('view_count');
        $tutorial->load(['category', 'category.translations', 'author', 'steps.hotspots', 'steps.stepTranslations', 'tags', 'translations']);

        $screenshotService = app(ProjectScreenshotService::class);
        $placeholderService = app(PlaceholderService::class);

        // Fortschritt für authentifizierte Benutzer laden
        $progress = null;
        if (auth()->check()) {
            $progress = TutorialProgress::firstOrNew([
                'user_id' => auth()->id(),
                'tutorial_id' => $tutorial->id,
            ]);
            $progress->load('currentStep');

            // Started_at setzen, wenn noch nicht gestartet
            if (!$progress->started_at) {
                $progress->started_at = now();
                $progress->save();
            }
        }

        // Steps als JSON-Array für den JavaScript-Player vorbereiten
        $stepsData = $tutorial->steps->map(function ($step) use ($locale, $screenshotService, $project, $placeholderService) {
            return [
                'id' => $step->id,
                'step_number' => $step->step_number,
                'title' => $placeholderService->resolve($step->getTranslationAttribute('title', $locale) ?? $step->title, $project, $locale),
                'instruction' => $placeholderService->resolve($step->getTranslationAttribute('instruction', $locale) ?? $step->instruction, $project, $locale),
                'tip' => $placeholderService->resolve($step->getTranslationAttribute('tip', $locale) ?? $step->tip, $project, $locale),
                'warning' => $placeholderService->resolve($step->getTranslationAttribute('warning', $locale) ?? $step->warning, $project, $locale),
                'screenshot_path' => $screenshotService->getTutorialStepScreenshot($step, $project),
                'hotspots' => $step->hotspots->map(function ($h) use ($placeholderService, $project, $locale) {
                    return [
                        'id' => $h->id,
                        'position_x' => $h->position_x,
                        'position_y' => $h->position_y,
                        'radius' => $h->radius ?? 30,
                        'label' => $placeholderService->resolve($h->label, $project, $locale),
                        'link' => $h->link,
                        'is_clickable' => $h->is_clickable,
                    ];
                })->values()->all(),
            ];
        })->values()->all();

        // Tutorial-Titel und -Beschreibung mit Platzhaltern ersetzen
        $tutorialTitle = $placeholderService->resolve($tutorial->getTranslationAttribute('title', $locale) ?? $tutorial->title, $project, $locale);
        $tutorialDescription = $placeholderService->resolve($tutorial->getTranslationAttribute('description', $locale) ?? $tutorial->description, $project, $locale);

        return view('tutorials.show', compact('tutorial', 'progress', 'stepsData', 'locale', 'tutorialTitle', 'tutorialDescription'));
    }

    /**
     * Fortschritt eines Tutorial-Schritts aktualisieren.
     */
    public function updateProgress(string $locale, Request $request, Tutorial $tutorial)
    {
        $request->validate([
            'step_id' => 'required|exists:tutorial_steps,id',
        ]);

        $progress = TutorialProgress::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'tutorial_id' => $tutorial->id,
            ],
            [
                'current_step_id' => $request->step_id,
                'last_visited_at' => now(),
            ]
        );

        return response()->json(['progress' => $progress]);
    }

    /**
     * Tutorial als abgeschlossen markieren.
     */
    public function complete(string $locale, Tutorial $tutorial)
    {
        $progress = TutorialProgress::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'tutorial_id' => $tutorial->id,
            ],
            [
                'completed' => true,
                'completed_at' => now(),
            ]
        );

        $tutorial->increment('completion_count');

        return response()->json(['message' => 'Tutorial abgeschlossen!']);
    }
}
