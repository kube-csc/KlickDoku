<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TutorialProject extends Model
{
    protected $table = 'tutorial_project';

    public $timestamps = false;

    protected $fillable = [
        'tutorial_id',
        'project_id',
        'primary',
    ];

    protected $casts = [
        'primary' => 'boolean',
    ];

    public function tutorial(): BelongsTo
    {
        return $this->belongsTo(Tutorial::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
