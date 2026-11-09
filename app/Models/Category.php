<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Category extends Model
{
    use Searchable;

    protected $fillable = ['name', 'slug', 'description', 'parent_id', 'sort_order'];

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'type' => 'category',
        ];
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function guides()
    {
        return $this->hasMany(Guide::class);
    }

    public function tutorials()
    {
        return $this->hasMany(Tutorial::class);
    }

    public function translations()
    {
        return $this->morphMany(ContentTranslation::class, 'translatable');
    }

    public function getTranslationAttribute(string $attribute, ?string $locale = null)
    {
        if (!$locale) {
            $locale = app()->getLocale();
        }

        $translation = $this->translations->firstWhere('locale', $locale);
        if ($translation && !is_null($translation->{$attribute})) {
            return $translation->{$attribute};
        }

        if (isset($this->{$attribute})) {
            return $this->{$attribute};
        }

        return null;
    }

    public function scopeForLocale($query, string $locale)
    {
        return $query->whereHas('translations', function ($q) use ($locale) {
            $q->where('locale', $locale);
        });
    }
}
