<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;

class Workshop extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $guarded = ["id"];
    protected $casts = [
        "published_at" => "datetime",
        "resources" => "array",
    ];

    public function getTypeAttribute()
    {
        return "workshop";
    }

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
        return $this->morphToMany(Tag::class, "taggable");
    }

    public function tracks(): MorphToMany
    {
        return $this->morphToMany(Track::class, "trackable");
    }

    public function challenge(): BelongsTo
    {
        return $this->belongsTo(Challenge::class);
    }

    public function scopeVisible($query)
    {
        return $query
            ->where("status", "!=", "draft")
            ->where("status", "!=", "archived");
    }

    public function scopeListed($query)
    {
        return $query
            ->where("status", "!=", "draft")
            ->where("status", "!=", "archived")
            ->where("status", "!=", "unlisted");
    }

    public function getLessonSectionsArray()
    {
        $grouped = $this->lessons->groupBy("section");

        if ($grouped->count() === 1) {
            return null;
        }

        return $grouped
            ->map(function ($lessons, $section) {
                return [
                    "name" => $section,
                    "lessons" => $lessons->pluck("id"),
                ];
            })
            ->values();
    }

    public function setImageUrlAttribute($value)
    {
        $attribute_name = "image_url";
        // or use your own disk, defined in config/filesystems.php
        $disk = "s3";
        // destination path relative to the disk above
        $destination_path = "workshops/cover-images";

        // if the image was erased
        if (empty($value)) {
            // delete the image from disk
            if (
                isset($this->{$attribute_name}) &&
                !empty($this->{$attribute_name})
            ) {
                \Storage::disk($disk)->delete($this->{$attribute_name});
            }
            // set null on database column
            $this->attributes[$attribute_name] = null;
        }

        // if a base64 was sent, store it in the db
        if (Str::startsWith($value, "data:image")) {
            // 0. Make the image
            $image = Image::make($value)
                ->encode("jpg", 90)
                ->resize(1280, 720, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

            // 1. Generate a filename.
            $filename = md5($value . time()) . ".jpg";

            // 2. Store the image on disk.
            \Storage::disk($disk)->put(
                $destination_path . "/" . $filename,
                $image->stream()
            );

            // 3. Delete the previous image, if there was one.
            if (
                isset($this->{$attribute_name}) &&
                !empty($this->{$attribute_name})
            ) {
                \Storage::disk($disk)->delete($this->{$attribute_name});
            }

            // 4. Save the public path to the database
            // but first, remove "public/" from the path, since we're pointing to it
            // from the root folder; that way, what gets saved in the db
            // is the public URL (everything that comes after the domain name)
            // $public_destination_path = Str::replaceFirst('public/', '', $destination_path);
            // $this->attributes[$attribute_name] = $public_destination_path . '/' . $filename;
            $this->attributes[$attribute_name] = \Storage::url(
                $destination_path . "/" . $filename
            );
        } elseif (!empty($value)) {
            // if value isn't empty, but it's not an image, assume it's the model value for that attribute.
            $this->attributes[$attribute_name] = $this->{$attribute_name};
        }
    }
}
