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
use Intervention\Image\Drivers\Gd\Decoder;

class AdminImageController extends Controller
{
    protected ImageManager $imageManager;

    public function __construct(ImageManager $imageManager)
    {
        $this->imageManager = $imageManager;
    }

    /**
     * Bild komprimieren und speichern.
     * Maximale Breite: 1920px, Qualität: 80%, Format: JPEG.
     */
    protected function compressAndStore($file, $directory)
    {
        $image = $this->imageManager->read($file->getPathname());

        // Auf maximale Breite von 1920px skalieren (Höhe proportional)
        $image->scaleWidth(1920, function ($constraint) {
            $constraint->upsideDown(); // Nicht vergrößern, nur verkleinern
        });

        $extension = $file->getClientOriginalExtension();
        $filename = Str::uuid() . '.' . $extension;
        $path = $directory . '/' . $filename;

        // Komprimiertes Bild speichern
        $contents = $image->encodeByExtension(80);
        \Illuminate\Support\Facades\Storage::disk('public')->put($path, $contents);

        return $path;
    }
    // Bild für einen Guide-Schritt hochladen
    public function storeGuideImage(Request $request, Guide $guide)
    {
        $validated = $request->validate([
            'step_id' => 'required|exists:guide_steps,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'caption' => 'nullable|string|max:255',
            'alt_text' => 'nullable|string|max:255',
        ]);

        $path = $this->compressAndStore($request->file('image'), 'guides/' . $guide->id);

        $sortOrder = GuideImage::where('guide_id', $guide->id)->max('sort_order') + 1;

        GuideImage::create([
            'guide_id' => $guide->id,
            'guide_step_id' => $validated['step_id'],
            'path' => $path,
            'filename' => pathinfo($path, PATHINFO_FILENAME),
            'caption' => $validated['caption'] ?? null,
            'alt_text' => $validated['alt_text'] ?? null,
            'sort_order' => $sortOrder,
        ]);

        return redirect()->back()
            ->with('success', 'Bild erfolgreich hochgeladen.');
    }

    // Bild für eine Anleitung (global) hochladen
    public function storeGuideGlobalImage(Request $request, Guide $guide)
    {
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
        $validated = $request->validate([
            'step_id' => 'required|exists:tutorial_steps,id',
            'screenshot' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $step = TutorialStep::findOrFail($validated['step_id']);
        $path = $this->compressAndStore($request->file('screenshot'), 'tutorials/' . $tutorial->id);

        // Alte Screenshot-Datei löschen
        if ($step->screenshot_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($step->screenshot_path);
        }

        $step->update([
            'screenshot_path' => $path,
        ]);

        return redirect()->back()
            ->with('success', 'Screenshot erfolgreich hochgeladen.');
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
    public function destroyTutorialScreenshot(Tutorial $tutorial, TutorialStep $step)
    {
        if ($step->screenshot_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($step->screenshot_path);
            $step->update(['screenshot_path' => null]);
        }

        return redirect()->back()
            ->with('success', 'Screenshot erfolgreich gelöscht.');
    }
}
