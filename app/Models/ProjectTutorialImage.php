<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectTutorialImage extends Model
{
    protected $fillable = [
        'project_id',
        'tutorial_step_id',
        'screenshot_path',
        'screenshot_width',
        'screenshot_height',
    ];

    protected $casts = [
        'screenshot_width' => 'integer',
        'screenshot_height' => 'integer',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function step(): BelongsTo
    {
        return $this->belongsTo(TutorialStep::class, 'tutorial_step_id');
    }
}
