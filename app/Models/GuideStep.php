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

    /**
     * Alle projekt-spezifischen Bilder für diesen Schritt.
     * Nutze projectImagesFor($project) für ein bestimmtes Projekt.
     */
    public function projectImages(Project $project)
    {
        return ProjectGuideImage::where('guide_step_id', $this->id)
            ->where('project_id', $project->id)
            ->orderBy('sort_order')->get();
    }

    /**
     * Eloquent-Beziehung: projekt-spezifische Bilder (für with() und Eager Loading).
     */
    public function projectGuideImages()
    {
        return $this->hasMany(ProjectGuideImage::class, 'guide_step_id')->orderBy('sort_order');
    }
}
