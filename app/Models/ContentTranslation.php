<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentTranslation extends Model
{
    protected $fillable = [
        'translatable_type',
        'translatable_id',
        'locale',
        'title',
        'description',
        'content',
        'meta_description',
        'meta_keywords',
    ];

    /**
     * Get the parent translatable model.
     */
    public function translatable()
    {
        return $this->morphTo();
    }

    /**
     * Get translations for a specific translatable.
     */
    public static function getByModel(string $type, int $id, ?string $locale = null): ?self
    {
        $query = self::where('translatable_type', $type)
            ->where('translatable_id', $id);

        if ($locale) {
            return $query->where('locale', $locale)->first();
        }

        return $query->first();
    }

    /**
     * Get all translations for a specific translatable.
     */
    public static function getAllByModel(string $type, int $id): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('translatable_type', $type)
            ->where('translatable_id', $id)
            ->get();
    }
}
