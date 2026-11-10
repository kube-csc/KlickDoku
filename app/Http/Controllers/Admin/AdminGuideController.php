<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Guide;
use App\Models\GuideStep;
use App\Models\Tag;
use App\Helpers\CacheHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AdminGuideController extends Controller
{
    public function index()
    {
        $guides = Guide::with('category')
            ->orderBy('published_at', 'desc')
            ->paginate(20);
        return view('admin.guides.index', compact('guides'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        return view('admin.guides.create', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'content' => 'required|string',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'published' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'og_image' => 'nullable|string|max:255',
            'steps' => 'nullable|array',
            'steps.*.title' => 'required_with:steps|string|max:255',
            'steps.*.description' => 'required_with:steps|string',
            'steps.*.tip' => 'nullable|string',
            'steps.*.warning' => 'nullable|string',
        ]);

        $guide = Guide::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category_id' => $validated['category_id'],
            'content' => $validated['content'],
            'slug' => Str::slug($validated['title']),
            'published' => $request->has('published'),
            'author_id' => auth()->id(),
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'og_image' => $validated['og_image'] ?? null,
        ]);

        // Tags zuweisen
        if (!empty($validated['tags'])) {
            $guide->tags()->sync($validated['tags']);
        }

        // Schritte erstellen
        if (!empty($validated['steps'])) {
            foreach ($validated['steps'] as $index => $step) {
                GuideStep::create([
                    'guide_id' => $guide->id,
                    'step_number' => $index + 1,
                    'title' => $step['title'],
                    'description' => $step['description'],
                    'tip' => $step['tip'] ?? null,
                    'warning' => $step['warning'] ?? null,
                ]);
            }
        }

        // Cache leeren
        CacheHelper::flushAll();

        return redirect()->route('admin.guides.index')
            ->with('success', 'Anleitung erfolgreich erstellt.');
    }

    public function edit(Guide $guide)
    {
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        $guide->load(['steps.images', 'steps.stepTranslations', 'tags', 'images']);
        return view('admin.guides.edit', compact('guide', 'categories', 'tags'));
    }

    public function update(Request $request, Guide $guide)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'content' => 'required|string',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'published' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'og_image' => 'nullable|string|max:255',
            'steps' => 'nullable|array',
            'steps.*.id' => 'nullable|integer',
            'steps.*.title' => 'required_with:steps|string|max:255',
            'steps.*.description' => 'required_with:steps|string',
            'steps.*.tip' => 'nullable|string',
            'steps.*.warning' => 'nullable|string',
        ]);

        $guide->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category_id' => $validated['category_id'],
            'content' => $validated['content'],
            'slug' => Str::slug($validated['title']),
            'published' => $request->has('published'),
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'og_image' => $validated['og_image'] ?? null,
        ]);

        // Tags synchronisieren
        if (!empty($validated['tags'])) {
            $guide->tags()->sync($validated['tags']);
        } else {
            $guide->tags()->detach();
        }

        // Schritte aktualisieren (bestehende Schritte updaten, neue erstellen, gelöschte entfernen)
        if (!empty($validated['steps'])) {
            // Alle bestehenden Schritt-IDs VOR dem Update speichern
            $existingStepIds = GuideStep::where('guide_id', $guide->id)->pluck('id')->toArray();
            
            $keptStepIds = [];
            foreach ($validated['steps'] as $index => $stepData) {
                $stepNumber = $index + 1;
                
                // Wenn eine ID vorhanden ist, bestehenden Schritt aktualisieren
                if (!empty($stepData['id'])) {
                    $existingStep = GuideStep::where('guide_id', $guide->id)
                        ->where('id', $stepData['id'])
                        ->first();
                    
                    if ($existingStep) {
                        $existingStep->update([
                            'step_number' => $stepNumber,
                            'title' => $stepData['title'],
                            'description' => $stepData['description'],
                            'tip' => $stepData['tip'] ?? null,
                            'warning' => $stepData['warning'] ?? null,
                        ]);
                        $keptStepIds[] = (int) $stepData['id'];
                    }
                } else {
                    // Neuer Schritt (ohne ID) erstellen
                    $newStep = GuideStep::create([
                        'guide_id' => $guide->id,
                        'step_number' => $stepNumber,
                        'title' => $stepData['title'],
                        'description' => $stepData['description'],
                        'tip' => $stepData['tip'] ?? null,
                        'warning' => $stepData['warning'] ?? null,
                    ]);
                    // Neue Schritt-ID zur Liste der zu behaltenden IDs hinzufügen
                    $keptStepIds[] = $newStep->id;
                }
            }
            
            // Nur bestehende Schritte löschen, die nicht mehr im Formular sind
            // (neue Schritte nicht löschen!)
            $stepsToDelete = array_diff($existingStepIds, $keptStepIds);
            if (!empty($stepsToDelete)) {
                $guide->steps()->whereIn('id', $stepsToDelete)->delete();
            }
        }

        // Cache leeren
        CacheHelper::flushAll();

        return redirect()->route('admin.guides.index')
            ->with('success', 'Anleitung erfolgreich aktualisiert.');
    }

    public function destroy(Guide $guide)
    {
        $guide->steps()->delete();
        $guide->tags()->detach();
        $guide->delete();

        // Cache leeren
        CacheHelper::flushAll();

        return redirect()->route('admin.guides.index')
            ->with('success', 'Anleitung erfolgreich gelöscht.');
    }
}
