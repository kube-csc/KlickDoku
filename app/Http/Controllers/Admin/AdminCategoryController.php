<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminCategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount(['guides', 'tutorials'])->orderBy('sort_order')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $parents = Category::whereNull('parent_id')->orderBy('name')->get();
        return view('admin.categories.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['sort_order'] = $validated['sort_order'] ?? Category::max('sort_order') + 1;

        Category::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategorie erfolgreich erstellt.');
    }

    public function edit(Category $category)
    {
        $parents = Category::whereNull('parent_id')->where('id', '!=', $category->id)->orderBy('name')->get();
        return view('admin.categories.edit', compact('category', 'parents'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'sort_order' => 'nullable|integer',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategorie erfolgreich aktualisiert.');
    }

    public function destroy(Category $category)
    {
        if ($category->guides()->count() > 0 || $category->tutorials()->count() > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Kategorie kann nicht gelöscht werden, da sie noch Anleitungen oder Tutorials enthält.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategorie erfolgreich gelöscht.');
    }
}
