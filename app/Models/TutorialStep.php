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
}
