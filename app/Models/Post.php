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

    protected static function booted()
    {
        static::saving(fn(self $post) => $post->fill(['html' => str($post->body)->markdown(
            [
                // Markdown 安全性設定：
                // - 'html_input' => 'strip'
                //     從使用者輸入移除原生 HTML，以防止 XSS（例如禁止使用者提供 <script>
                //     或內聯事件處理器）。
                // - 'allow_unsafe_links' => false
                //     禁止 javascript: 或 data: 等不安全的連結類型，避免透過惡意 URL
                //     觸發 XSS 或其他攻擊。
                // - 'max_nesting_level' => 5
                //     限制巢狀層級深度，避免極深巢狀輸入導致大量運算或記憶體使用（可緩解
                //     特定類型的 DoS 攻擊）。
                // - 'max_delimiters_per_line' => 10
                //     限制每行分隔符的數量，避免極長或重複分隔符導致渲染或處理問題。
                // 修改這些值前，請評估安全性與效能的權衡，並根據應用需求做測試。
                'html_input' => 'strip',
                'allow_unsafe_links' => false,
                'max_nesting_level' => 5,
                'max_delimiters_per_line' => 10
            ]
        )]));
    }

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

    public function showRoute(array $queryParameters = []): string
    {
        return route('posts.show', [$this, Str::slug($this->title), ...$queryParameters]);
    }
}
