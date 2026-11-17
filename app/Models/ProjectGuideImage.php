<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectGuideImage extends Model
{
    protected $fillable = [
        'project_id',
        'guide_step_id',
        'filename',
        'path',
        'caption',
        'alt_text',
        'sort_order',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function step(): BelongsTo
    {
        return $this->belongsTo(GuideStep::class, 'guide_step_id');
    }
}
