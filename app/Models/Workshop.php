<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Workshop extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function tracks(): MorphToMany
    {
        return $this->morphToMany(Track::class, 'trackable');
    }

    public function scopeVisible($query)
    {
        return $query->where('status', '!=', 'draft')->where('status', '!=', 'archived');
    }
}
