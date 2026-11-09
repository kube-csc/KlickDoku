<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TutorialProgress extends Model
{
    protected $fillable = [
        'user_id', 'tutorial_id', 'current_step_id', 'completed',
        'completed_at', 'started_at', 'last_visited_at'
    ];

    protected $casts = [
        'completed' => 'boolean',
        'completed_at' => 'datetime',
        'started_at' => 'datetime',
        'last_visited_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tutorial()
    {
        return $this->belongsTo(Tutorial::class);
    }

    public function currentStep()
    {
        return $this->belongsTo(TutorialStep::class, 'current_step_id');
    }
}
