<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'subdomain',
        'domain',
        'app_name',
        'club_name',
        'logo_path',
        'primary_color',
        'secondary_color',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function terms(): HasMany
    {
        return $this->hasMany(ProjectTerm::class);
    }

    public function guides(): BelongsToMany
    {
        return $this->belongsToMany(Guide::class, 'guide_project')->withTimestamps();
    }

    public function tutorials(): BelongsToMany
    {
        return $this->belongsToMany(Tutorial::class, 'tutorial_project')->withTimestamps();
    }

    public function guideImages(): HasMany
    {
        return $this->hasMany(ProjectGuideImage::class);
    }

    public function tutorialImages(): HasMany
    {
        return $this->hasMany(ProjectTutorialImage::class);
    }

    /**
     * Users zugewiesen zu diesem Projekt (Many-to-Many).
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo_path ? asset($this->logo_path) : null;
    }

    public function getTerm(string $key, ?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        $term = $this->terms->firstWhere('placeholder_key', $key);
        if (!$term) {
            return null;
        }

        $attribute = $locale === 'en' ? 'value_en' : 'value_de';
        return $term->{$attribute} ?? $term->value_de;
    }
}
