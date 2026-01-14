<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'body',
        'html',
    ];

    /**
     * Get the user that owns the Post
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all of the comments for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    protected function title(): Attribute
    {
        return Attribute::make(
            get: fn($value) => ucwords($value),
        );
    }

    protected function body(): Attribute
    {
        return Attribute::make(
            set: fn($value) => [
                'body' => $value,
                'html' => str($value)->markdown(),
            ]
        );
    }

    public function showRoute(array $queryParameters = []): string
    {
        return route('posts.show', [$this, Str::slug($this->title), ...$queryParameters]);
    }
}
