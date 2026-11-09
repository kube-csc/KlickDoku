<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentTranslation;
use App\Models\Guide;
use App\Models\Tutorial;
use App\Models\Page;
use App\Models\Category;
use Illuminate\Http\Request;

class AdminTranslationController extends Controller
{
    /**
     * Übersetzungsübersicht anzeigen.
     */
    public function index()
    {
        $locales = config('app.supported_locales', ['de', 'en']);
        $defaultLocale = config('app.locale', 'de');

        // Zähle Inhalte pro Typ und Sprache
        $stats = [
            'guides' => [
                'total' => Guide::count(),
                'translations' => [],
            ],
            'tutorials' => [
                'total' => Tutorial::count(),
                'translations' => [],
            ],
            'pages' => [
                'total' => Page::count(),
                'translations' => [],
            ],
            'categories' => [
                'total' => Category::count(),
                'translations' => [],
            ],
        ];

        foreach ($locales as $locale) {
            if ($locale === $defaultLocale) {
                continue; // Standardsprache hat keine extra Übersetzungen
            }

            $stats['guides']['translations'][$locale] = ContentTranslation::where('translatable_type', Guide::class)
                ->where('locale', $locale)
                ->count();
            $stats['tutorials']['translations'][$locale] = ContentTranslation::where('translatable_type', Tutorial::class)
                ->where('locale', $locale)
                ->count();
            $stats['pages']['translations'][$locale] = ContentTranslation::where('translatable_type', Page::class)
                ->where('locale', $locale)
                ->count();
            $stats['categories']['translations'][$locale] = ContentTranslation::where('translatable_type', Category::class)
                ->where('locale', $locale)
                ->count();
        }

        return view('admin.translations.index', compact('stats', 'locales', 'defaultLocale'));
    }

    /**
     * Übersetzungen für einen bestimmten Inhaltstyp anzeigen.
     */
    public function show(string $type)
    {
        $modelClass = match ($type) {
            'guides' => Guide::class,
            'tutorials' => Tutorial::class,
            'pages' => Page::class,
            'categories' => Category::class,
            default => abort(404),
        };

        $locales = config('app.supported_locales', ['de', 'en']);
        $defaultLocale = config('app.locale', 'de');

        // Hole alle Inhalte dieses Typs
        $items = $modelClass::latest()->paginate(20);

        return view('admin.translations.show', compact('items', 'type', 'modelClass', 'locales', 'defaultLocale'));
    }

    /**
     * Übersetzung für einen Inhalt speichern/aktualisieren.
     */
    public function store(Request $request, string $type, int $id)
    {
        $modelClass = match ($type) {
            'guides' => Guide::class,
            'tutorials' => Tutorial::class,
            'pages' => Page::class,
            'categories' => Category::class,
            default => abort(404),
        };

        $translatable = $modelClass::findOrFail($id);

        $validated = $request->validate([
            'locale' => 'required|in:' . implode(',', config('app.supported_locales', ['de', 'en'])),
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'content' => 'nullable|string',
        ]);

        // Standardsprache darf nicht übersetzt werden
        if ($validated['locale'] === config('app.locale', 'de')) {
            return back()->withErrors(['locale' => 'Die Standardsprache benötigt keine Übersetzung.']);
        }

        // Finde oder erstelle Übersetzung
        $translation = ContentTranslation::updateOrCreate(
            [
                'translatable_type' => get_class($translatable),
                'translatable_id' => $translatable->id,
                'locale' => $validated['locale'],
            ],
            [
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'content' => $validated['content'] ?? null,
            ]
        );

        return back()->with('success', 'Übersetzung erfolgreich gespeichert.');
    }

    /**
     * Übersetzung löschen.
     */
    public function destroy(string $type, int $id, string $locale)
    {
        $modelClass = match ($type) {
            'guides' => Guide::class,
            'tutorials' => Tutorial::class,
            'pages' => Page::class,
            'categories' => Category::class,
            default => abort(404),
        };

        $translatable = $modelClass::findOrFail($id);

        ContentTranslation::where([
            'translatable_type' => get_class($translatable),
            'translatable_id' => $translatable->id,
            'locale' => $locale,
        ])->delete();

        return back()->with('success', 'Übersetzung erfolgreich gelöscht.');
    }
}
