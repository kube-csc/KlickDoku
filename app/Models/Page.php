<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Page extends Model
{
    use Searchable;

    protected $fillable = ['title', 'slug', 'content', 'meta_description', 'meta_keywords', 'status', 'author_id', 'sort_order'];

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'meta_description' => $this->meta_description,
            'type' => 'page',
        ];
    }

    public function shouldBeSearchable()
    {
        return $this->status === 'published';
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
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
