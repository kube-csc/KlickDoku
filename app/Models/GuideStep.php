<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuideStep extends Model
{
    protected $fillable = ['guide_id', 'step_number', 'title', 'description', 'tip', 'warning'];

    public function guide()
    {
        return $this->belongsTo(Guide::class);
    }

    public function images()
    {
        return $this->hasMany(GuideImage::class)->orderBy('sort_order');
    }

    public function stepTranslations()
    {
        return $this->hasMany(GuideStepTranslation::class, 'guide_step_id');
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
