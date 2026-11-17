<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TutorialStep extends Model
{
    protected $fillable = [
        'tutorial_id', 'step_number', 'title', 'instruction',
        'screenshot_path', 'screenshot_width', 'screenshot_height',
        'tip', 'warning', 'auto_advance', 'auto_advance_delay_ms'
    ];

    protected $casts = [
        'auto_advance' => 'boolean',
        'screenshot_width' => 'integer',
        'screenshot_height' => 'integer',
    ];

    protected $attributes = [
        'screenshot_path' => null,
        'screenshot_width' => null,
        'screenshot_height' => null,
    ];

    public function tutorial()
    {
        return $this->belongsTo(Tutorial::class);
    }

    public function hotspots()
    {
        return $this->hasMany(TutorialHotspot::class)->orderBy('sort_order');
    }

    public function stepTranslations()
    {
        return $this->hasMany(TutorialStepTranslation::class, 'tutorial_step_id');
    }

    /**
     * Übersetzten Attributwert zurückgeben oder den Originalwert als Fallback.
     */
    public function getTranslationAttribute(string $attribute, ?string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        $translation = $this->stepTranslations()
            ->where('locale', $locale)
            ->first();

        if ($translation && isset($translation->{$attribute}) && $translation->{$attribute}) {
            return $translation->{$attribute};
        }

        return $this->{$attribute} ?? null;
    }

    /**
     * Projekt-spezifischen Screenshot-Pfad zurückgeben, oder Master als Fallback.
     */
    public function projectScreenshot(Project $project): ?string
    {
        $img = ProjectTutorialImage::where('tutorial_step_id', $this->id)
            ->where('project_id', $project->id)->first();
        return $img ? $img->screenshot_path : $this->screenshot_path;
    }

    /**
     * Eloquent-Beziehung: projekt-spezifische Screenshots (für with() und Eager Loading).
     */
    public function projectTutorialImages()
    {
        return $this->hasMany(ProjectTutorialImage::class, 'tutorial_step_id');
    }
}
