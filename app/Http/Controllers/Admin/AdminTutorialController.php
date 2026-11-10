<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Tutorial;
use App\Models\TutorialStep;
use App\Models\TutorialHotspot;
use App\Helpers\CacheHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminTutorialController extends Controller
{
    public function index()
    {
        $tutorials = Tutorial::with('category')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('admin.tutorials.index', compact('tutorials'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        return view('admin.tutorials.create', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'published' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'og_image' => 'nullable|string|max:255',
            'steps' => 'nullable|array',
            'steps.*.title' => 'required_with:steps|string|max:255',
            'steps.*.instruction' => 'required_with:steps|string',
            'steps.*.hotspots' => 'nullable|array',
            'steps.*.hotspots.*.selector' => 'nullable|string',
            'steps.*.hotspots.*.label' => 'nullable|string',
            'steps.*.hotspots.*.link' => 'nullable|string|max:255|sometimes',
            'steps.*.hotspots.*.hotspot_type' => 'nullable|string|sometimes',
            'steps.*.hotspots.*.position_x' => 'nullable|integer|sometimes',
            'steps.*.hotspots.*.position_y' => 'nullable|integer|sometimes',
            'steps.*.screenshot_path' => 'nullable|string|max:255|sometimes',
            'steps.*.screenshot_width' => 'nullable|integer|sometimes',
            'steps.*.screenshot_height' => 'nullable|integer|sometimes',
        ]);

        $tutorial = Tutorial::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category_id' => $validated['category_id'],
            'slug' => Str::slug($validated['title']),
            'published' => $request->has('published'),
            'author_id' => auth()->id(),
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'og_image' => $validated['og_image'] ?? null,
        ]);

        // Tags zuweisen
        if (!empty($validated['tags'])) {
            $tutorial->tags()->sync($validated['tags']);
        }

        // Schritte erstellen
        if (!empty($validated['steps'])) {
            foreach ($validated['steps'] as $index => $step) {
                $tutorialStep = TutorialStep::create([
                    'tutorial_id' => $tutorial->id,
                    'step_number' => $index + 1,
                    'title' => $step['title'],
                    'instruction' => $step['instruction'],
                    'screenshot_path' => $step['screenshot_path'] ?? '',
                    'screenshot_width' => $step['screenshot_width'] ?? 0,
                    'screenshot_height' => $step['screenshot_height'] ?? 0,
                ]);

                // Hotspots erstellen
                if (!empty($step['hotspots'])) {
                    foreach ($step['hotspots'] as $hotspot) {
                        TutorialHotspot::create([
                            'tutorial_step_id' => $tutorialStep->id,
                            'selector' => $hotspot['selector'],
                            'label' => $hotspot['label'],
                            'link' => !empty($hotspot['link']) ? $hotspot['link'] : null,
                            'hotspot_type' => $hotspot['hotspot_type'] ?? 'click',
                            'position_x' => $hotspot['position_x'] ?? 0,
                            'position_y' => $hotspot['position_y'] ?? 0,
                        ]);
                    }
                }
            }
        }

        CacheHelper::flushAll();

        return redirect()->route('admin.tutorials.index')
            ->with('success', 'Tutorial erfolgreich erstellt.');
    }

    public function edit(Tutorial $tutorial)
    {
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        $tutorial->load(['steps.hotspots', 'steps.stepTranslations', 'tags']);
        return view('admin.tutorials.edit', compact('tutorial', 'categories', 'tags'));
    }

    public function update(Request $request, Tutorial $tutorial)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'published' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'og_image' => 'nullable|string|max:255',
            'steps' => 'nullable|array',
            'steps.*.title' => 'required_with:steps|string|max:255',
            'steps.*.instruction' => 'required_with:steps|string',
            'steps.*.hotspots' => 'nullable|array',
            'steps.*.hotspots.*.selector' => 'nullable|string',
            'steps.*.hotspots.*.label' => 'nullable|string',
            'steps.*.hotspots.*.link' => 'nullable|string|max:255|sometimes',
            'steps.*.hotspots.*.hotspot_type' => 'nullable|string|sometimes',
            'steps.*.hotspots.*.position_x' => 'nullable|integer|sometimes',
            'steps.*.hotspots.*.position_y' => 'nullable|integer|sometimes',
            'steps.*.screenshot_path' => 'nullable|string|max:255|sometimes',
            'steps.*.screenshot_width' => 'nullable|integer|sometimes',
            'steps.*.screenshot_height' => 'nullable|integer|sometimes',
        ]);

        $tutorial->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category_id' => $validated['category_id'],
            'slug' => Str::slug($validated['title']),
            'published' => $request->has('published'),
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'og_image' => $validated['og_image'] ?? null,
        ]);

        // Tags synchronisieren
        if (!empty($validated['tags'])) {
            $tutorial->tags()->sync($validated['tags']);
        } else {
            $tutorial->tags()->detach();
        }

        // Schritte aktualisieren
        if (!empty($validated['steps'])) {
            $tutorial->steps()->delete();
            foreach ($validated['steps'] as $index => $step) {
                $tutorialStep = TutorialStep::create([
                    'tutorial_id' => $tutorial->id,
                    'step_number' => $index + 1,
                    'title' => $step['title'],
                    'instruction' => $step['instruction'],
                    'screenshot_path' => $step['screenshot_path'] ?? '',
                    'screenshot_width' => $step['screenshot_width'] ?? 0,
                    'screenshot_height' => $step['screenshot_height'] ?? 0,
                ]);

                // Hotspots erstellen
                if (!empty($step['hotspots'])) {
                    foreach ($step['hotspots'] as $hotspot) {
                        TutorialHotspot::create([
                            'tutorial_step_id' => $tutorialStep->id,
                            'selector' => $hotspot['selector'],
                            'label' => $hotspot['label'],
                            'link' => !empty($hotspot['link']) ? $hotspot['link'] : null,
                            'hotspot_type' => $hotspot['hotspot_type'] ?? 'click',
                            'position_x' => $hotspot['position_x'] ?? 0,
                            'position_y' => $hotspot['position_y'] ?? 0,
                        ]);
                    }
                }
            }
        }

        CacheHelper::flushAll();

        return redirect()->route('admin.tutorials.index')
            ->with('success', 'Tutorial erfolgreich aktualisiert.');
    }

    public function destroy(Tutorial $tutorial)
    {
        $tutorial->tags()->detach();
        $tutorial->delete();

        return redirect()->route('admin.tutorials.index')
            ->with('success', 'Tutorial erfolgreich gelöscht.');
    }
}
