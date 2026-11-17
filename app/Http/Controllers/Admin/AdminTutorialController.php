<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Tutorial;
use App\Models\TutorialStep;
use App\Models\TutorialHotspot;
use App\Services\PlaceholderService;
use App\Helpers\CacheHelper;
use App\Rules\ValidHotspotLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;

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
            'og_image' => 'nullable|image|max:5120',
            'steps' => 'nullable|array',
            'steps.*.title' => 'required_with:steps|string|max:255',
            'steps.*.instruction' => 'required_with:steps|string',
            'steps.*.hotspots' => 'nullable|array',
            'steps.*.hotspots.*.selector' => 'nullable|string',
            'steps.*.hotspots.*.label' => 'nullable|string',
            'steps.*.hotspots.*.link' => ['nullable', 'string', 'max:2048', new ValidHotspotLink()],
            'steps.*.hotspots.*.hotspot_type' => 'nullable|string|sometimes',
            'steps.*.hotspots.*.position_x' => 'nullable|integer|sometimes',
            'steps.*.hotspots.*.position_y' => 'nullable|integer|sometimes',
            'steps.*.screenshot_path' => 'nullable|string|max:255|sometimes',
            'steps.*.screenshot_width' => 'nullable|integer|sometimes',
            'steps.*.screenshot_height' => 'nullable|integer|sometimes',
            'steps.*.screenshot_data' => 'nullable|string|sometimes',
        ]);

        // OpenGraph-Bild hochladen
        $ogImagePath = null;
        if ($request->hasFile('og_image')) {
            try {
                $ogImagePath = $this->compressAndStore($request->file('og_image'), 'tutorials/' . 'new');
            } catch (\Exception $e) {
                $request->session()->flash('error', 'Fehler beim Hochladen des Vorschau-Bilds: ' . $e->getMessage());
            }
        }

        $tutorial = Tutorial::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category_id' => $validated['category_id'],
            'slug' => slugFromTitle($validated['title']),
            'published' => $request->has('published'),
            'author_id' => auth()->id(),
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'og_image' => $ogImagePath,
        ]);

        // OG-Bild-Pfad nach Erstellung korrigieren (von 'new' zur tatsächlichen ID)
        if ($ogImagePath) {
            $newPath = str_replace('tutorials/new/', 'tutorials/' . $tutorial->id . '/', $ogImagePath);
            Storage::disk('public')->move($ogImagePath, $newPath);
            $tutorial->update(['og_image' => $newPath]);
        }

        // Tags zuweisen
        if (!empty($validated['tags'])) {
            $tutorial->tags()->sync($validated['tags']);
        }

        // Schritte erstellen
        if (!empty($validated['steps'])) {
            foreach ($validated['steps'] as $index => $step) {
                // Screenshot aus Base64-Daten speichern (von region-screenshot-js)
                $screenshotPath = $step['screenshot_path'] ?? '';
                $screenshotWidth = $step['screenshot_width'] ?? 0;
                $screenshotHeight = $step['screenshot_height'] ?? 0;

                if (!empty($step['screenshot_data'])) {
                    // Base64-Daten extrahieren (data:image/png;base64,...)
                    $base64Data = $step['screenshot_data'];
                    if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $typeMatches)) {
                        $extension = $typeMatches[1];
                        $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
                        $imageData = base64_decode($base64Data);

                        if ($imageData !== false) {
                            // Bildabmessungen ermitteln
                            $imageSize = getimagesizefromstring($imageData);
                            if ($imageSize !== false) {
                                $screenshotWidth = $imageSize[0];
                                $screenshotHeight = $imageSize[1];
                            }

                            // Datei speichern
                            $filename = 'tutorials/' . $tutorial->id . '/step_' . ($index + 1) . '_' . Str::random(10) . '.' . $extension;
                            Storage::disk('public')->put($filename, $imageData);
                            $screenshotPath = $filename;
                        }
                    }
                }

                $tutorialStep = TutorialStep::create([
                    'tutorial_id' => $tutorial->id,
                    'step_number' => $index + 1,
                    'title' => $step['title'],
                    'instruction' => $step['instruction'],
                    'screenshot_path' => $screenshotPath,
                    'screenshot_width' => $screenshotWidth,
                    'screenshot_height' => $screenshotHeight,
                ]);

                // Hotspots erstellen
                if (!empty($step['hotspots'])) {
                    $placeholderService = app(PlaceholderService::class);
                    foreach ($step['hotspots'] as $hotspot) {
                        // Automatische Platzhalter-Erkennung anwenden
                        $hotspot = $placeholderService->autoDetectHotspotPlaceholders($hotspot);
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
            'og_image' => 'nullable|image|max:5120',
            'steps' => 'nullable|array',
            'steps.*.title' => 'required_with:steps|string|max:255',
            'steps.*.instruction' => 'required_with:steps|string',
            'steps.*.hotspots' => 'nullable|array',
            'steps.*.hotspots.*.selector' => 'nullable|string',
            'steps.*.hotspots.*.label' => 'nullable|string',
            'steps.*.hotspots.*.link' => ['nullable', 'string', 'max:2048', new ValidHotspotLink()],
            'steps.*.hotspots.*.hotspot_type' => 'nullable|string|sometimes',
            'steps.*.hotspots.*.position_x' => 'nullable|integer|sometimes',
            'steps.*.hotspots.*.position_y' => 'nullable|integer|sometimes',
            'steps.*.screenshot_path' => 'nullable|string|max:255|sometimes',
            'steps.*.screenshot_width' => 'nullable|integer|sometimes',
            'steps.*.screenshot_height' => 'nullable|integer|sometimes',
            'steps.*.screenshot_data' => 'nullable|string|sometimes',
        ]);

        // OpenGraph-Bild hochladen
        $ogImagePath = $tutorial->og_image;
        if ($request->hasFile('og_image')) {
            try {
                // Altes OG-Bild löschen
                if ($tutorial->og_image) {
                    Storage::disk('public')->delete($tutorial->og_image);
                }
                $ogImagePath = $this->compressAndStore($request->file('og_image'), 'tutorials/' . $tutorial->id);
            } catch (\Exception $e) {
                Log::error('OG Image Fehler: ' . $e->getMessage());
                $request->session()->flash('error', 'Fehler beim Hochladen des Vorschau-Bilds.');
                $ogImagePath = $tutorial->og_image;
            }
        }

        $tutorial->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category_id' => $validated['category_id'],
            'slug' => slugFromTitle($validated['title']),
            'published' => $request->has('published'),
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'og_image' => $ogImagePath,
        ]);

        // Tags synchronisieren
        if (!empty($validated['tags'])) {
            $tutorial->tags()->sync($validated['tags']);
        } else {
            $tutorial->tags()->detach();
        }

        // Schritte aktualisieren
        if (!empty($validated['steps'])) {
            // Alte Screenshot-Pfade sammeln (aus dem Formular gesendete Pfade sollen erhalten bleiben)
            $keepPaths = [];
            foreach ($validated['steps'] as $step) {
                if (!empty($step['screenshot_path'])) {
                    $keepPaths[] = $step['screenshot_path'];
                }
            }

            // Nur alte Screenshot-Dateien löschen, die nicht mehr verwendet werden
            foreach ($tutorial->steps as $oldStep) {
                if ($oldStep->screenshot_path && !in_array($oldStep->screenshot_path, $keepPaths)) {
                    Storage::disk('public')->delete($oldStep->screenshot_path);
                }
            }

            $tutorial->steps()->delete();
            foreach ($validated['steps'] as $index => $step) {
                // Screenshot-Pfad aus dem Formular verwenden (bereits hochgeladene Screenshots)
                $screenshotPath = $step['screenshot_path'] ?? null;
                $screenshotWidth = $step['screenshot_width'] ?? null;
                $screenshotHeight = $step['screenshot_height'] ?? null;

                // Falls neuer Screenshot als Base64-Daten gesendet (von create-Seite)
                if (empty($screenshotPath) && !empty($step['screenshot_data'])) {
                    $base64Data = $step['screenshot_data'];
                    if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $typeMatches)) {
                        $extension = $typeMatches[1];
                        $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
                        $imageData = base64_decode($base64Data);

                        if ($imageData !== false) {
                            $imageSize = getimagesizefromstring($imageData);
                            if ($imageSize !== false) {
                                $screenshotWidth = $imageSize[0];
                                $screenshotHeight = $imageSize[1];
                            }

                            $filename = 'tutorials/' . $tutorial->id . '/step_' . ($index + 1) . '_' . Str::random(10) . '.' . $extension;
                            Storage::disk('public')->put($filename, $imageData);
                            $screenshotPath = $filename;
                        }
                    }
                }

                $tutorialStep = TutorialStep::create([
                    'tutorial_id' => $tutorial->id,
                    'step_number' => $index + 1,
                    'title' => $step['title'],
                    'instruction' => $step['instruction'],
                    'screenshot_path' => $screenshotPath,
                    'screenshot_width' => $screenshotWidth,
                    'screenshot_height' => $screenshotHeight,
                ]);

                // Hotspots erstellen
                if (!empty($step['hotspots'])) {
                    $placeholderService = app(PlaceholderService::class);
                    foreach ($step['hotspots'] as $hotspot) {
                        // Automatische Platzhalter-Erkennung anwenden
                        $hotspot = $placeholderService->autoDetectHotspotPlaceholders($hotspot);
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

    /**
     * OG-Bild eines Tutorials löschen (unterstützt AJAX und Redirect).
     */
    public function destroyOgImage($tutorialId)
    {
        $tutorial = Tutorial::findOrFail($tutorialId);
        if ($tutorial->og_image) {
            Storage::disk('public')->delete($tutorial->og_image);
            $tutorial->update(['og_image' => null]);
        }

        CacheHelper::flushAll();

        if (request()->wantsJson() || request()->header('Accept') === 'application/json') {
            return response()->json(['success' => true, 'message' => 'Vorschau-Bild erfolgreich gelöscht.']);
        }

        return redirect()->back()
            ->with('success', 'Vorschau-Bild erfolgreich gelöscht.');
    }

    /**
     * Bild komprimieren und speichern.
     * Maximale Breite: 1920px, Qualität: 80%.
     */
    protected function compressAndStore($file, $directory)
    {
        $manager = ImageManager::gd();
        $image = $manager->read($file->getPathname());

        // Auf maximale Breite von 1920px skalieren
        $image->scaleDown(1920, null);

        $extension = strtolower($file->getClientOriginalExtension());
        $filename = Str::uuid() . '.' . $extension;
        $path = $directory . '/' . $filename;

        // Komprimiertes Bild speichern
        $contents = $image->encodeByExtension($extension, quality: 80);
        Storage::disk('public')->put($path, $contents);

        return $path;
    }
}
