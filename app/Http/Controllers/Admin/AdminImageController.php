<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guide;
use App\Models\GuideImage;
use App\Models\GuideStep;
use App\Models\Tutorial;
use App\Models\TutorialStep;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;

class AdminImageController extends Controller
{
    /**
     * Bild komprimieren und speichern.
     * Maximale Breite: 1920px, Qualität: 80%, Format: JPEG.
     */
    protected function compressAndStore($file, $directory)
    {
        $manager = ImageManager::gd();
        $image = $manager->read($file->getPathname());

        // Auf maximale Breite von 1920px skalieren (Höhe proportional, nicht vergrößern)
        $image->scaleDown(1920, null);

        $extension = $file->getClientOriginalExtension();
        $filename = Str::uuid() . '.' . $extension;
        $path = $directory . '/' . $filename;

        // Komprimiertes Bild speichern (Intervention Image v3: encodeByExtension mit quality named arg)
        $contents = $image->encodeByExtension($extension, quality: 80);
        \Illuminate\Support\Facades\Storage::disk('public')->put($path, $contents);

        return $path;
    }
    // Bild für einen Guide-Schritt hochladen
    public function storeGuideImage(Request $request, Guide $guide)
    {
        \Log::info('storeGuideImage aufgerufen', [
            'guide_id' => $guide->id,
            'step_id' => $request->input('step_id'),
            'all_input' => $request->all(),
            'has_image' => $request->hasFile('image'),
        ]);

        $validated = $request->validate([
            'step_id' => 'required|exists:guide_steps,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'caption' => 'nullable|string|max:255',
            'alt_text' => 'nullable|string|max:255',
        ]);

        $path = $this->compressAndStore($request->file('image'), 'guides/' . $guide->id);

        $sortOrder = GuideImage::where('guide_id', $guide->id)->max('sort_order') + 1;

        $imageData = [
            'guide_id' => $guide->id,
            'guide_step_id' => $validated['step_id'],
            'path' => $path,
            'filename' => pathinfo($path, PATHINFO_FILENAME),
            'caption' => $validated['caption'] ?? null,
            'alt_text' => $validated['alt_text'] ?? null,
            'sort_order' => $sortOrder,
        ];

        \Log::info('Speichere GuideImage mit Daten:', $imageData);

        $image = GuideImage::create($imageData);

        \Log::info('GuideImage gespeichert:', [
            'id' => $image->id,
            'guide_step_id' => $image->guide_step_id,
            'guide_id' => $image->guide_id,
        ]);

        // Für AJAX-Anfragen JSON zurückgeben
        if ($request->headers->get('X-Requested-With') === 'XMLHttpRequest' || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Bild erfolgreich hochgeladen.',
                'image' => [
                    'id' => $image->id,
                    'guide_step_id' => $image->guide_step_id,
                    'path' => $image->path,
                    'caption' => $image->caption,
                ]
            ], 200);
        }

        return redirect()->back()
            ->with('success', 'Bild erfolgreich hochgeladen.');
    }

    // Bild für eine Anleitung (global) hochladen
    public function storeGuideGlobalImage(Request $request, Guide $guide)
    {
        \Log::info('storeGuideGlobalImage aufgerufen', [
            'guide_id' => $guide->id,
            'all_input' => $request->all(),
            'has_image' => $request->hasFile('image'),
        ]);

        $validated = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'caption' => 'nullable|string|max:255',
            'alt_text' => 'nullable|string|max:255',
        ]);

        $path = $this->compressAndStore($request->file('image'), 'guides/' . $guide->id);

        $sortOrder = GuideImage::where('guide_id', $guide->id)->whereNull('guide_step_id')->max('sort_order') + 1;

        GuideImage::create([
            'guide_id' => $guide->id,
            'guide_step_id' => null,
            'path' => $path,
            'filename' => pathinfo($path, PATHINFO_FILENAME),
            'caption' => $validated['caption'] ?? null,
            'alt_text' => $validated['alt_text'] ?? null,
            'sort_order' => $sortOrder,
        ]);

        return redirect()->back()
            ->with('success', 'Bild erfolgreich hochgeladen.');
    }

    // Screenshot für Tutorial-Schritt hochladen
    public function storeTutorialScreenshot(Request $request, Tutorial $tutorial)
    {
        \Log::info('storeTutorialScreenshot aufgerufen', [
            'tutorial_id' => $tutorial->id,
            'step_id' => $request->input('step_id'),
            'has_file' => $request->hasFile('screenshot'),
            'file_name' => $request->file('screenshot')?->getClientOriginalName() ?? 'keine',
            'file_size' => $request->file('screenshot')?->getSize() ?? 0,
            'file_type' => $request->file('screenshot')?->getMimeType() ?? 'keine',
        ]);

        try {
            $validated = $request->validate([
                'step_id' => 'required|exists:tutorial_steps,id',
                'screenshot' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            ]);

            \Log::info('Validierung erfolgreich', ['step_id' => $validated['step_id']]);

            $step = TutorialStep::findOrFail($validated['step_id']);
            \Log::info('TutorialStep gefunden', ['step_id' => $step->id, 'current_path' => $step->screenshot_path ?? 'null']);

            $path = $this->compressAndStore($request->file('screenshot'), 'tutorials/' . $tutorial->id);
            \Log::info('Bild gespeichert', ['path' => $path]);

            // Alte Screenshot-Datei löschen
            if ($step->screenshot_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($step->screenshot_path);
                \Log::info('Alte Datei gelöscht', ['old_path' => $step->screenshot_path]);
            }

            $step->update([
                'screenshot_path' => $path,
            ]);
            \Log::info('Datenbank aktualisiert', ['new_path' => $path]);

            // JSON-Antwort für AJAX
            if ($request->headers->get('X-Requested-With') === 'XMLHttpRequest' || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Screenshot erfolgreich hochgeladen.',
                    'path' => $path,
                ]);
            }

            return redirect()->back()
                ->with('success', 'Screenshot erfolgreich hochgeladen.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validierungsfehler beim Screenshot-Upload', [
                'errors' => $e->errors(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Validierungsfehler',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Fehler beim Screenshot-Upload', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Hochladen: ' . $e->getMessage(),
            ], 500);
        }
    }

    // Screenshot für Guide-Schritt hochladen
    public function storeGuideScreenshot(Request $request, Guide $guide)
    {
        \Log::info('storeGuideScreenshot aufgerufen', [
            'guide_id' => $guide->id,
            'step_id' => $request->input('step_id'),
            'has_file' => $request->hasFile('screenshot'),
            'file_name' => $request->file('screenshot')?->getClientOriginalName() ?? 'keine',
            'file_size' => $request->file('screenshot')?->getSize() ?? 0,
            'file_type' => $request->file('screenshot')?->getMimeType() ?? 'keine',
        ]);

        try {
            $validated = $request->validate([
                'step_id' => 'required|exists:guide_steps,id',
                'screenshot' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            ]);

            \Log::info('Validierung erfolgreich', ['step_id' => $validated['step_id']]);

            $path = $this->compressAndStore($request->file('screenshot'), 'guides/' . $guide->id);
            \Log::info('Bild gespeichert', ['path' => $path]);

            $sortOrder = GuideImage::where('guide_id', $guide->id)->where('guide_step_id', $validated['step_id'])->max('sort_order') + 1;

            $image = GuideImage::create([
                'guide_id' => $guide->id,
                'guide_step_id' => $validated['step_id'],
                'path' => $path,
                'filename' => pathinfo($path, PATHINFO_FILENAME),
                'caption' => 'Screenshot',
                'alt_text' => null,
                'sort_order' => $sortOrder,
            ]);
            \Log::info('GuideImage erstellt', ['id' => $image->id, 'path' => $path]);

            // JSON-Antwort für AJAX
            if ($request->headers->get('X-Requested-With') === 'XMLHttpRequest' || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Screenshot erfolgreich hochgeladen.',
                    'path' => $path,
                ]);
            }

            return redirect()->back()
                ->with('success', 'Screenshot erfolgreich hochgeladen.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validierungsfehler beim Guide-Screenshot-Upload', [
                'errors' => $e->errors(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Validierungsfehler',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Fehler beim Guide-Screenshot-Upload', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Hochladen: ' . $e->getMessage(),
            ], 500);
        }
    }

    // Bild löschen
    public function destroyGuideImage(GuideImage $image)
    {
        $guideId = $image->guide_id;
        \Illuminate\Support\Facades\Storage::disk('public')->delete($image->path);
        $image->delete();

        return redirect()->route('admin.guides.edit', $guideId)
            ->with('success', 'Bild erfolgreich gelöscht.');
    }

    // Screenshot von Tutorial-Schritt löschen
    public function destroyTutorialScreenshot(Tutorial $tutorial, $step)
    {
        try {
            $stepModel = TutorialStep::findOrFail($step);
            
            \Log::info('destroyTutorialScreenshot aufgerufen', [
                'tutorial_id' => $tutorial->id,
                'step_id' => $stepModel->id,
                'screenshot_path' => $stepModel->screenshot_path,
            ]);

            if ($stepModel->screenshot_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($stepModel->screenshot_path);
                $stepModel->update(['screenshot_path' => null]);
                \Log::info('Screenshot gelöscht', ['path' => $stepModel->screenshot_path]);
            }

            return redirect()->back()
                ->with('success', 'Screenshot erfolgreich gelöscht.');
        } catch (\Exception $e) {
            \Log::error('Fehler beim Löschen des Screenshots', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()
                ->with('error', 'Fehler beim Löschen des Screenshots: ' . $e->getMessage());
        }
    }
}
