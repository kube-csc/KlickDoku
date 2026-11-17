<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuideProject extends Model
{
    protected $table = 'guide_project';

    public $timestamps = false;

    protected $fillable = [
        'guide_id',
        'project_id',
        'primary',
    ];

    protected $casts = [
        'primary' => 'boolean',
    ];

    public function guide(): BelongsTo
    {
        return $this->belongsTo(Guide::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
