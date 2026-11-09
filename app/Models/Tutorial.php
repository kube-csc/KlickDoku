<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Tutorial extends Model
{
    use Searchable;

    protected $fillable = [
        'category_id', 'title', 'slug', 'description', 'thumbnail_path',
        'estimated_duration_minutes', 'status', 'author_id',
        'view_count', 'completion_count',
        'meta_title', 'meta_description', 'og_image'
    ];

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'type' => 'tutorial',
            'category_id' => $this->category_id,
            'category_name' => $this->category ? $this->category->name : null,
            'author_name' => $this->author ? $this->author->name : null,
        ];
    }

    public function shouldBeSearchable()
    {
        return $this->status === 'published';
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
        return $this->hasMany(TutorialStep::class)->orderBy('step_number');
    }

    public function progressRecords()
    {
        return $this->hasMany(TutorialProgress::class);
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
