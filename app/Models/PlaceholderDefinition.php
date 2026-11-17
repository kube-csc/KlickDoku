<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlaceholderDefinition extends Model
{
    protected $fillable = [
        'key',
        'description_de',
        'description_en',
        'default_value_de',
        'default_value_en',
        'category',
    ];

    public function getDescriptionAttribute(): ?string
    {
        $locale = app()->getLocale();
        if ($locale === 'en' && $this->description_en) {
            return $this->description_en;
        }
        return $this->description_de;
    }

    public function getDefaultValueAttribute(): ?string
    {
        $locale = app()->getLocale();
        if ($locale === 'en' && $this->default_value_en) {
            return $this->default_value_en;
        }
        return $this->default_value_de;
    }

    public function scopeCategory($query, ?string $category)
    {
        if (!$category) {
            return $query;
        }
        return $query->where('category', $category);
    }
}
