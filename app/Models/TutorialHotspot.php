<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TutorialHotspot extends Model
{
    protected $fillable = [
        'tutorial_step_id', 'hotspot_type', 'position_x', 'position_y',
        'radius', 'label', 'cursor_type', 'click_count', 'is_clickable', 'sort_order'
    ];

    protected $casts = [
        'is_clickable' => 'boolean',
    ];

    public function step()
    {
        return $this->belongsTo(TutorialStep::class, 'tutorial_step_id');
    }
}
