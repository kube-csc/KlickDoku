<?php

namespace App\Http\Controllers;

use App\Models\Page;

class PageController extends Controller
{
    /**
     * Statische Seite nach Slug anzeigen.
     */
    public function show(string $locale, string $slug)
    {
        $page = Page::where('slug', $slug)
            ->where('status', 'published')
            ->with(['author', 'tags', 'translations'])
            ->firstOrFail();

        return view('pages.show', compact('page'));
    }
}
