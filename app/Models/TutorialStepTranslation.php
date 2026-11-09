<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TutorialStepTranslation extends Model
{
    protected $table = 'tutorial_steps_translations';

    protected $fillable = [
        'tutorial_step_id', 'locale', 'title', 'instruction', 'tip', 'warning',
    ];

    public function tutorialStep()
    {
        return $this->belongsTo(TutorialStep::class, 'tutorial_step_id');
    }
}
