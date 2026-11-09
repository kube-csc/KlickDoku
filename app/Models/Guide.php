<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Guide extends Model
{
    use Searchable;

    protected $fillable = [
        'category_id', 'title', 'slug', 'description', 'content',
        'version', 'project_ref', 'status', 'author_id',
        'published_at', 'view_count',
        'meta_title', 'meta_description', 'og_image'
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'content' => $this->content,
            'type' => 'guide',
            'category_id' => $this->category_id,
            'category_name' => $this->category ? $this->category->name : null,
            'author_name' => $this->author ? $this->author->name : null,
        ];
    }

    public function shouldBeSearchable()
    {
        return $this->status === 'published' && $this->published_at <= now();
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function steps()
    {
        return $this->hasMany(GuideStep::class)->orderBy('step_number');
    }

    public function images()
    {
        return $this->hasMany(GuideImage::class)->orderBy('sort_order');
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function ratings()
    {
        return $this->morphMany(Rating::class, 'rateable');
    }

    public function feedbacks()
    {
        return $this->morphMany(Feedback::class, 'feedbackable');
    }

    public function translations()
    {
        return $this->morphMany(ContentTranslation::class, 'translatable');
    }

    /**
     * Get a translated attribute, falling back to the model's own attribute.
     */
    public function getTranslationAttribute(string $attribute, ?string $locale = null)
    {
        if (!$locale) {
            $locale = app()->getLocale();
        }

        $translation = $this->translations->firstWhere('locale', $locale);
        if ($translation && !is_null($translation->{$attribute})) {
            return $translation->{$attribute};
        }

        // Fallback: eigene Spalte
        if (isset($this->{$attribute})) {
            return $this->{$attribute};
        }

        return null;
    }

    /**
     * Scope: filter by locale (includes items with translation OR default locale match).
     */
    public function scopeForLocale($query, string $locale)
    {
        return $query->where(function ($q) use ($locale) {
            // Items whose translation exists for the locale
            $q->whereHas('translations', function ($q) use ($locale) {
                $q->where('locale', $locale);
            });
        });
    }
}
