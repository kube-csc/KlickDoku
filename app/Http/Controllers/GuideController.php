<?php

namespace App\Http\Controllers;

use App\Models\Guide;
use App\Models\Category;
use App\Helpers\CacheHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Barryvdh\DomPDF\Facade\Pdf;

class GuideController extends Controller
{
    /**
     * Alle veröffentlichten Anleitungen auflisten (mit Caching).
     */
    public function index(Request $request)
    {
        // Cache-Key basierend auf den Filter-Parametern
        $cacheKey = 'webseite:guides_index_' . md5(
            ($request->category ?? '') . 
            ($request->search ?? '') . 
            app()->getLocale()
        );

        return CacheHelper::remember($cacheKey, now()->addMinutes(30), function () use ($request) {
            $query = Guide::with(['category', 'category.translations', 'author', 'steps', 'translations'])
                ->where('status', 'published')
                ->orderBy('published_at', 'desc');

            // Filter nach Kategorie
            if ($request->filled('category')) {
                $query->where('category_id', $request->category);
            }

            // Suche im Titel und der Beschreibung
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            $guides = $query->paginate(12);
            $categories = Category::whereNull('parent_id')->with('translations')->orderBy('sort_order')->get();

            return view('guides.index', compact('guides', 'categories'));
        });
    }

    /**
     * Einzelne Anleitung anzeigen (mit Caching).
     */
    public function show(string $locale, string $slug)
    {
        $guide = Guide::where('slug', $slug)->firstOrFail();
        if ($guide->status !== 'published') {
            abort(404);
        }

        $cacheKey = 'webseite:guide_show_' . $slug . '_' . app()->getLocale();

        return CacheHelper::remember($cacheKey, now()->addMinutes(30), function () use ($guide) {
            // View-Count erhöhen
            $guide->increment('view_count');

            $guide->load(['category', 'category.translations', 'author', 'steps.images', 'steps.stepTranslations', 'tags', 'translations']);
            
            // Nur genehmigte Kommentare laden
            $comments = $guide->comments()
                ->with('user')
                ->where('status', 'approved')
                ->whereNull('parent_id')
                ->orderBy('created_at', 'asc')
                ->get()
                ->load('replies.user');

            return view('guides.show', compact('guide', 'comments'));
        });
    }

    /**
     * Anleitung als PDF exportieren.
     */
    public function pdf(string $locale, string $slug)
    {
        $guide = Guide::where('slug', $slug)->firstOrFail();
        if ($guide->status !== 'published') {
            abort(404);
        }

        $guide->load(['category', 'steps.images', 'tags']);

        $pdf = Pdf::loadView('guides.pdf', compact('guide'))
            ->setPaper('a4', 'portrait')
            ->output();

        return response($pdf, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $guide->slug . '.pdf"');
    }
}
