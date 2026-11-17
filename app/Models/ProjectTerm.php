<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectTerm extends Model
{
    protected $fillable = [
        'project_id',
        'placeholder_key',
        'value_de',
        'value_en',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function getValueAttribute(): ?string
    {
        $locale = app()->getLocale();
        if ($locale === 'en' && $this->value_en) {
            return $this->value_en;
        }
        return $this->value_de;
    }
}
