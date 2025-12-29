<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Testing\TestResponse;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Assert;

/**
 * 測試服務提供者
 * 
 * 在測試環境中為 Inertia 和 TestResponse 類別添加自訂測試斷言方法。
 * 提供便捷的方法來驗證傳遞給 Inertia 視圖的資源資料是否正確。
 */
class TestingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     * 
     * 在服務容器中註冊服務（此提供者不需要）
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     * 
     * 在應用程式啟動時執行服務的初始化邏輯。
     * 只在測試環境中定義自訂宏和測試斷言方法。
     */
    public function boot(): void
    {
        // 只在單位測試環境中執行，避免在其他環境（如生產環境）中載入不必要的代碼
        if (! $this->app->runningUnitTests()) {
            return;
        }

        AssertableInertia::macro('hasResource', function (string $key, JsonResource $resource) {
            // 確保指定的 key 存在於傳遞給 Inertia 的屬性中
            Assert::assertTrue(Arr::has($this->prop(), $key));

            // 確保資源的內容與預期的資源完全匹配
            $compiledResource = $resource->response()->getData(true);
            Assert::assertEquals($compiledResource, $this->prop($key));

            // 回傳 $this 以支援鏈式呼叫
            return $this;
        });

        AssertableInertia::macro('hasPaginatedResource', function (string $key, JsonResource $resourceCollection) {
            // 確保指定的 key 存在於傳遞給 Inertia 的屬性中
            Assert::assertTrue(Arr::has($this->prop(), $key));

            // 確保分頁資源的 data 鍵存在
            Assert::assertTrue(Arr::has($this->prop(), "{$key}.data"));

            // 驗證分頁資源具有必要的結構：data（資料）、links（分頁連結）、meta（元資料）
            Assert::assertEqualsCanonicalizing(['data', 'links', 'meta'], array_keys($this->prop($key)));

            // 驗證 data 部分的資源內容是否與預期的資源完全匹配
            return $this->hasResource("{$key}.data", $resourceCollection);
        });

        /**
         * 為 TestResponse 添加測試斷言方法
         * 這是對上述 Inertia 宏的包裝，使其可以直接在回應物件中使用
         */
        TestResponse::macro('assertHasResource', function (string $key, JsonResource $resource) {
            // 使用 assertInertia 方法配合定義的 hasResource 宏進行斷言
            return $this->assertInertia(fn(AssertableInertia $page) => $page->hasResource($key, $resource));
        });

        TestResponse::macro('assertHasPaginatedResource', function (string $key, ResourceCollection $resource) {
            // 使用 assertInertia 方法配合定義的 hasPaginatedResource 宏進行斷言
            return $this->assertInertia(fn(AssertableInertia $page) => $page->hasPaginatedResource($key, $resource));
        });
    }
}
