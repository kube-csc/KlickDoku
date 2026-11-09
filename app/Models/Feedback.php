<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $fillable = ['user_id', 'feedbackable_id', 'feedbackable_type', 'type', 'subject', 'body', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function feedbackable()
    {
        return $this->morphTo();
    }
}
