<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Guide;
use App\Models\Tutorial;
use App\Http\Resources\GuideResource;
use App\Http\Resources\TutorialResource;
use App\Http\Resources\CategoryResource;
use App\Services\PlaceholderService;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function __construct(
        protected PlaceholderService $placeholderService
    ) {
    }

    /**
     * Alle veröffentlichten Anleitungen als JSON.
     */
    public function guides(Request $request)
    {
        $project = $request->attributes->get('project');

        $query = Guide::with(['category', 'author', 'steps', 'tags'])
            ->where('status', 'published')
            ->orderBy('published_at', 'desc');

        // Projekt-Filter: nur zugewiesene Guides anzeigen
        if ($project) {
            $query->forProject($project);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $guides = $query->paginate($request->get('per_page', 15));

        return GuideResource::collection($guides)
            ->additional(['request' => request()->only('search', 'page', 'category_id')]);
    }

    /**
     * Einzelne Anleitung als JSON.
     */
    public function guideShow(Guide $guide)
    {
        $project = request()->attributes->get('project');

        if ($guide->status !== 'published') {
            return response()->json(['error' => 'Anleitung nicht gefunden'], 404);
        }

        // Projekt-Zuweisung prüfen
        if ($project && !$guide->projects->contains($project)) {
            return response()->json(['error' => 'Anleitung nicht für dieses Projekt verfügbar'], 404);
        }

        $guide->load(['category', 'author', 'steps.images', 'tags']);

        return new GuideResource($guide);
    }

    /**
     * Alle veröffentlichten Tutorials als JSON.
     */
    public function tutorials(Request $request)
    {
        $project = $request->attributes->get('project');

        $query = Tutorial::with(['category', 'author', 'steps.hotspots', 'tags'])
            ->where('status', 'published')
            ->orderBy('published_at', 'desc');

        // Projekt-Filter: nur zugewiesene Tutorials anzeigen
        if ($project) {
            $query->forProject($project);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $tutorials = $query->paginate($request->get('per_page', 15));

        return TutorialResource::collection($tutorials)
            ->additional(['request' => request()->only('page', 'category_id')]);
    }

    /**
     * Einzelnes Tutorial als JSON.
     */
    public function tutorialShow(Tutorial $tutorial)
    {
        $project = request()->attributes->get('project');

        if ($tutorial->status !== 'published') {
            return response()->json(['error' => 'Tutorial nicht gefunden'], 404);
        }

        // Projekt-Zuweisung prüfen
        if ($project && !$tutorial->projects->contains($project)) {
            return response()->json(['error' => 'Tutorial nicht für dieses Projekt verfügbar'], 404);
        }

        $tutorial->load(['category', 'author', 'steps.hotspots', 'tags']);

        return new TutorialResource($tutorial);
    }

    /**
     * Alle Kategorien als JSON.
     */
    public function categories()
    {
        $categories = Category::orderBy('sort_order')->get();

        return CategoryResource::collection($categories);
    }

    /**
     * Globale Suche als JSON.
     */
    public function search(Request $request)
    {
        $project = $request->attributes->get('project');
        $query = $request->input('q', '');

        if (strlen($query) < 2) {
            return response()->json([
                'guides' => [],
                'tutorials' => [],
                'total' => 0,
            ]);
        }

        // Scout-Suche ohne Projekt-Filter, dann nachträglich filtern
        $guideIds = Guide::search($query)->take(50)->ids();
        $guideQuery = Guide::whereIn('id', $guideIds)->where('status', 'published');
        if ($project) {
            $guideQuery->forProject($project);
        }
        $guides = $guideQuery->take(10)->get()
            ->map(fn($g) => new GuideResource($g->load(['category', 'tags'])));

        $tutorialIds = Tutorial::search($query)->take(50)->ids();
        $tutorialQuery = Tutorial::whereIn('id', $tutorialIds)->where('status', 'published');
        if ($project) {
            $tutorialQuery->forProject($project);
        }
        $tutorials = $tutorialQuery->take(10)->get()
            ->map(fn($t) => new TutorialResource($t->load(['category', 'tags'])));

        return response()->json([
            'guides' => $guides,
            'tutorials' => $tutorials,
            'total' => $guides->count() + $tutorials->count(),
            'query' => $query,
        ]);
    }
}
