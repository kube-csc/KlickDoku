<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Guide;
use App\Models\Tutorial;
use App\Http\Resources\GuideResource;
use App\Http\Resources\TutorialResource;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    /**
     * Alle veröffentlichten Anleitungen als JSON.
     */
    public function guides(Request $request)
    {
        $query = Guide::with(['category', 'author', 'steps', 'tags'])
            ->where('status', 'published')
            ->orderBy('published_at', 'desc');

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
        if ($guide->status !== 'published') {
            return response()->json(['error' => 'Anleitung nicht gefunden'], 404);
        }

        $guide->load(['category', 'author', 'steps.images', 'tags']);

        return new GuideResource($guide);
    }

    /**
     * Alle veröffentlichten Tutorials als JSON.
     */
    public function tutorials(Request $request)
    {
        $query = Tutorial::with(['category', 'author', 'steps.hotspots', 'tags'])
            ->where('status', 'published')
            ->orderBy('published_at', 'desc');

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
        if ($tutorial->status !== 'published') {
            return response()->json(['error' => 'Tutorial nicht gefunden'], 404);
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
        $query = $request->input('q', '');

        if (strlen($query) < 2) {
            return response()->json([
                'guides' => [],
                'tutorials' => [],
                'total' => 0,
            ]);
        }

        $guides = Guide::search($query)
            ->where('status', 'published')
            ->take(10)
            ->get()
            ->map(fn($g) => new GuideResource($g->load(['category', 'tags'])));

        $tutorials = Tutorial::search($query)
            ->where('status', 'published')
            ->take(10)
            ->get()
            ->map(fn($t) => new TutorialResource($t->load(['category', 'tags'])));

        return response()->json([
            'guides' => $guides,
            'tutorials' => $tutorials,
            'total' => $guides->count() + $tutorials->count(),
            'query' => $query,
        ]);
    }
}
