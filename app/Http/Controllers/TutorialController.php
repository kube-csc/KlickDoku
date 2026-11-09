<?php

namespace App\Http\Controllers;

use App\Models\Tutorial;
use App\Models\Category;
use App\Models\TutorialProgress;
use Illuminate\Http\Request;

class TutorialController extends Controller
{
    /**
     * Alle veröffentlichten Tutorials auflisten.
     */
    public function index(Request $request)
    {
        $query = Tutorial::with(['category', 'category.translations', 'author', 'translations'])
            ->where('status', 'published')
            ->orderBy('created_at', 'desc');

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
        $tutorial = Tutorial::where('slug', $slug)->firstOrFail();
        if ($tutorial->status !== 'published') {
            abort(404);
        }

        $tutorial->increment('view_count');
        $tutorial->load(['category', 'category.translations', 'author', 'steps.hotspots', 'steps.stepTranslations', 'tags', 'translations']);

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
        $stepsData = $tutorial->steps->map(function ($step) use ($locale) {
            return [
                'id' => $step->id,
                'step_number' => $step->step_number,
                'title' => $step->getTranslationAttribute('title', $locale) ?? $step->title,
                'instruction' => $step->getTranslationAttribute('instruction', $locale) ?? $step->instruction,
                'tip' => $step->getTranslationAttribute('tip', $locale) ?? $step->tip,
                'warning' => $step->getTranslationAttribute('warning', $locale) ?? $step->warning,
                'screenshot_path' => $step->screenshot_path,
                'hotspots' => $step->hotspots->map(function ($h) {
                    return [
                        'id' => $h->id,
                        'position_x' => $h->position_x,
                        'position_y' => $h->position_y,
                        'radius' => $h->radius ?? 30,
                        'label' => $h->label,
                        'is_clickable' => $h->is_clickable,
                    ];
                })->values()->all(),
            ];
        })->values()->all();

        return view('tutorials.show', compact('tutorial', 'progress', 'stepsData', 'locale'));
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
