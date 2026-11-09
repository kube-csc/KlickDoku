<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['name', 'slug'];

    public function guides()
    {
        return $this->morphedByMany(Guide::class, 'taggable');
    }

    public function tutorials()
    {
        return $this->morphedByMany(Tutorial::class, 'taggable');
    }

    public function pages()
    {
        return $this->morphedByMany(Page::class, 'taggable');
    }
}
