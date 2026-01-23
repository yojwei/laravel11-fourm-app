<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    /**
     * 使用模型工廠
     *
     * 允許在測試或開發時透過 `Topic::factory()` 快速產生模型實例。
     * @use HasFactory<\Database\Factories\TopicFactory>
     */
    use HasFactory;

    /**
     * 取得路由鍵名稱
     *
     * Laravel 用於路由模型綁定的欄位名稱，預設為 `id`。
     * 這裡回傳 `slug`，代表在路由中會使用 `topic:slug` 作為辨識。
     *
     * @return string 回傳用於路由綁定的欄位名稱（slug）
     */
    public function getRouteKeyName(): string
    {
        return "slug";
    }
}
