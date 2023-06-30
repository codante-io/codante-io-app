<?php

namespace App\Models;

use App\Traits\Reactable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    use HasFactory;
    use Reactable;

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, "taggable");
    }
}
