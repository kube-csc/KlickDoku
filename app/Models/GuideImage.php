<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuideImage extends Model
{
    protected $fillable = ['guide_step_id', 'guide_id', 'filename', 'path', 'caption', 'alt_text', 'sort_order'];

    public function guide()
    {
        return $this->belongsTo(Guide::class);
    }

    public function step()
    {
        return $this->belongsTo(GuideStep::class, 'guide_step_id');
    }
}
