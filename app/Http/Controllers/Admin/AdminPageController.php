<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminPageController extends Controller
{
    public function index()
    {
        $pages = Page::orderBy('sort_order')->get();
        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.pages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'show_in_nav' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['slug'] = Str::slug($validated['title']);
        $validated['show_in_nav'] = $request->has('show_in_nav');
        $validated['sort_order'] = $validated['sort_order'] ?? Page::max('sort_order') + 1;

        Page::create($validated);

        return redirect()->route('admin.pages.index')
            ->with('success', 'Seite erfolgreich erstellt.');
    }

    public function edit(Page $page)
    {
        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, Page $page)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'show_in_nav' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['slug'] = Str::slug($validated['title']);
        $validated['show_in_nav'] = $request->has('show_in_nav');

        $page->update($validated);

        return redirect()->route('admin.pages.index')
            ->with('success', 'Seite erfolgreich aktualisiert.');
    }

    public function destroy(Page $page)
    {
        $page->delete();

        return redirect()->route('admin.pages.index')
            ->with('success', 'Seite erfolgreich gelöscht.');
    }
}
