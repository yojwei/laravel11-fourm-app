<?php

namespace App\Providers;

use App\Models\Post;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;
use Nette\Utils\Json;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 關閉 JsonResource 預設外層包裝，回應保持扁平結構。
        JsonResource::withoutWrapping();

        // 非正式環境禁止延遲載入，及早發現 N+1 問題。
        Model::preventLazyLoading(!app()->isProduction());

        // 強制多型關聯使用固定別名，避免資料表存入完整類名。
        Relation::enforceMorphMap([
            'post' => Post::class,
            'comment' => Comment::class,
            'user' => User::class,
        ]);
    }
}
