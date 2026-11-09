<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Guide;
use App\Models\Tutorial;
use App\Models\Page;
use App\Helpers\CacheHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Laravel\Scout\Builder;

class SearchController extends Controller
{
    /**
     * Globale Volltextsuche mit Laravel Scout (mit Caching).
     */
    public function search(Request $request)
    {
        $query = $request->input('q', '');
        $type = $request->input('type', ''); // Filter: guide, tutorial, page, category
        $categoryId = $request->input('category_id', '');

        $guides = collect();
        $tutorials = collect();
        $pages = collect();
        $categories = collect();
        $totalResults = 0;

        if (strlen($query) >= 2) {
            // Cache-Key basierend auf Suchparametern
            $cacheKey = 'webseite:search_' . md5($query . $type . $categoryId . app()->getLocale());

            $results = CacheHelper::remember($cacheKey, now()->addMinutes(15), function () use ($query, $type, $categoryId) {
                $guideResults = collect();
                $tutorialResults = collect();
                $pageResults = collect();
                $categoryResults = collect();

                // Guide-Suche
                if ($type === '' || $type === 'guide') {
                    $guideSearch = Guide::search($query);
                    if ($categoryId) {
                        $guideSearch->where('category_id', $categoryId);
                    }
                    $guideResults = $guideSearch->take(10)->get();
                    if ($guideResults->isNotEmpty()) {
                        $guideIds = $guideResults->modelKeys();
                        $loadedGuides = Guide::with(['category', 'tags'])
                            ->whereIn('id', $guideIds)
                            ->get()
                            ->keyBy('id');
                        $guideResults = collect($guideIds)->map(function ($id) use ($loadedGuides) {
                            return $loadedGuides->get($id);
                        })->filter()->values();
                    }
                }

                // Tutorial-Suche
                if ($type === '' || $type === 'tutorial') {
                    $tutorialSearch = Tutorial::search($query);
                    if ($categoryId) {
                        $tutorialSearch->where('category_id', $categoryId);
                    }
                    $tutorialResults = $tutorialSearch->take(10)->get();
                    if ($tutorialResults->isNotEmpty()) {
                        $tutorialIds = $tutorialResults->modelKeys();
                        $loadedTutorials = Tutorial::with(['category', 'tags'])
                            ->whereIn('id', $tutorialIds)
                            ->get()
                            ->keyBy('id');
                        $tutorialResults = collect($tutorialIds)->map(function ($id) use ($loadedTutorials) {
                            return $loadedTutorials->get($id);
                        })->filter()->values();
                    }
                }

                // Page-Suche
                if ($type === '' || $type === 'page') {
                    $pageResults = Page::search($query)->take(10)->get();
                }

                // Category-Suche
                if ($type === '' || $type === 'category') {
                    $categoryResults = Category::search($query)->take(10)->get();
                }

                return [
                    'guides' => $guideResults,
                    'tutorials' => $tutorialResults,
                    'pages' => $pageResults,
                    'categories' => $categoryResults,
                ];
            });

            $guides = $results['guides'];
            $tutorials = $results['tutorials'];
            $pages = $results['pages'];
            $categories = $results['categories'];
            $totalResults = $guides->count() + $tutorials->count() + $pages->count() + $categories->count();
        }

        $allCategories = Category::orderBy('name')->get();

        return view('search.results', compact(
            'guides',
            'tutorials',
            'pages',
            'categories',
            'allCategories',
            'query',
            'type',
            'categoryId',
            'totalResults'
        ));
    }
}
