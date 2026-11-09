<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuideStepTranslation extends Model
{
    protected $table = 'guide_steps_translations';

    protected $fillable = [
        'guide_step_id', 'locale', 'title', 'instruction', 'tip', 'warning',
    ];

    public function guideStep()
    {
        return $this->belongsTo(GuideStep::class, 'guide_step_id');
    }
}
