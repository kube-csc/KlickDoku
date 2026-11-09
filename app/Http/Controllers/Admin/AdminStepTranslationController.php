<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guide;
use App\Models\GuideStepTranslation;
use App\Models\Tutorial;
use App\Models\TutorialStepTranslation;
use App\Helpers\CacheHelper;
use Illuminate\Http\Request;

class AdminStepTranslationController extends Controller
{
    /**
     * Schritt-Übersetzung für eine Anleitung speichern oder aktualisieren.
     */
    public function storeGuideStep(Request $request, Guide $guide, $stepId)
    {
        $validated = $request->validate([
            'locale' => 'required|in:de,en',
            'title' => 'nullable|string|max:255',
            'instruction' => 'nullable|string',
            'tip' => 'nullable|string',
            'warning' => 'nullable|string',
        ]);

        GuideStepTranslation::updateOrCreate(
            [
                'guide_step_id' => $stepId,
                'locale' => $validated['locale'],
            ],
            [
                'title' => $validated['title'] ?? null,
                'instruction' => $validated['instruction'] ?? null,
                'tip' => $validated['tip'] ?? null,
                'warning' => $validated['warning'] ?? null,
            ]
        );

        CacheHelper::flushAll();

        return redirect()->back()
            ->with('success', 'Schritt-Übersetzung erfolgreich gespeichert.');
    }

    /**
     * Schritt-Übersetzung für ein Tutorial speichern oder aktualisieren.
     */
    public function storeTutorialStep(Request $request, Tutorial $tutorial, $stepId)
    {
        $validated = $request->validate([
            'locale' => 'required|in:de,en',
            'title' => 'nullable|string|max:255',
            'instruction' => 'nullable|string',
            'tip' => 'nullable|string',
            'warning' => 'nullable|string',
        ]);

        TutorialStepTranslation::updateOrCreate(
            [
                'tutorial_step_id' => $stepId,
                'locale' => $validated['locale'],
            ],
            [
                'title' => $validated['title'] ?? null,
                'instruction' => $validated['instruction'] ?? null,
                'tip' => $validated['tip'] ?? null,
                'warning' => $validated['warning'] ?? null,
            ]
        );

        CacheHelper::flushAll();

        return redirect()->back()
            ->with('success', 'Schritt-Übersetzung erfolgreich gespeichert.');
    }

    /**
     * Schritt-Übersetzung für eine Anleitung löschen.
     */
    public function destroyGuideStep(Guide $guide, $stepId, string $locale)
    {
        GuideStepTranslation::where('guide_step_id', $stepId)
            ->where('locale', $locale)
            ->delete();

        CacheHelper::flushAll();

        return redirect()->back()
            ->with('success', 'Schritt-Übersetzung erfolgreich gelöscht.');
    }

    /**
     * Schritt-Übersetzung für ein Tutorial löschen.
     */
    public function destroyTutorialStep(Tutorial $tutorial, $stepId, string $locale)
    {
        TutorialStepTranslation::where('tutorial_step_id', $stepId)
            ->where('locale', $locale)
            ->delete();

        CacheHelper::flushAll();

        return redirect()->back()
            ->with('success', 'Schritt-Übersetzung erfolgreich gelöscht.');
    }
}
