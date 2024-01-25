<?php

namespace App\Models;

use App\Http\Resources\CommentResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\Validator;

class Comment extends Model
{
    use HasFactory;

    protected $guarded = ["id"];

    public function User()
    {
        return $this->belongsTo(User::class);
    }

    public function Commentable()
    {
        return $this->morphTo();
    }

    public function Replies()
    {
        return $this->hasMany(Comment::class, "replying_to");
    }

    public static function getComments($commentableClass, $commentableId)
    {
        $commentable = $commentableClass::findOrFail($commentableId); // find if the commentable exists
        $comments = $commentable->comments()->get();

        return CommentResource::collection($comments);
    }

    public static function validateCommentable($commentableType)
    {
        $commentableClass = "App\\Models\\" . $commentableType;

        $validator = Validator::make(
            ["commentable_type" => $commentableClass],
            [
                "commentable_type" => [
                    function ($attribute, $value, $fail) {
                        if (!class_exists($value)) {
                            $fail("Commentable model does not exist.");
                        } elseif (
                            !in_array(
                                "App\\Traits\\Commentable",
                                class_uses($value)
                            )
                        ) {
                            $fail("Model is not commentable.");
                        }
                    },
                ],
            ]
        );

        $validator->validate();
        return $commentableClass;
    }

    public static function validateReply(string $replyingTo)
    {
        $validator = Validator::make(
            ["replying_to" => $replyingTo],
            [
                "replying_to" => [
                    function ($attribute, $value, $fail) {
                        // dd($value);
                        if ($value) {
                            $replyingToComment = Comment::where(
                                "id",
                                $value
                            )->first();
                            if (
                                $replyingToComment &&
                                $replyingToComment->replying_to !== null
                            ) {
                                $fail("Invalid replying_to value.");
                            }
                        }
                    },
                ],
            ]
        );

        $validator->validate();
    }

    public static function createComment(
        User $user,
        string $commentableClass,
        string $commentableId,
        string $comment,
        string $replyingTo = null
    ) {
        $commentable = $commentableClass::findOrFail($commentableId);
        $comment = $commentable->create($comment, $user, $replyingTo);

        return new CommentResource($comment);
    }
}
