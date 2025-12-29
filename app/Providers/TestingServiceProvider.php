<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Testing\TestResponse;
use Inertia\Testing\AssertableInertia;

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
            // 從 Inertia 視圖中獲取所有傳遞的屬性
            $props = $this->toArray()['props'];

            // 將資源編譯為陣列格式（使用相同的序列化方式作為檢測）
            $compiledResource = $resource->response()->getData(true);

            // 斷言：確保指定的 key 存在於傳遞給 Inertia 的屬性中
            \PHPUnit\Framework\Assert::assertArrayHasKey($key, $props, "Key {$key} not passed as a property to Inertia");

            // 斷言：確保資源的內容與預期的資源完全匹配
            \PHPUnit\Framework\Assert::assertEquals(
                $compiledResource,
                $props[$key],
                "The resource for key {$key} does not match the expected resource."
            );

            // 回傳 $this 以支援鏈式呼叫
            return $this;
        });

        AssertableInertia::macro('hasPaginatedResource', function (string $key, ResourceCollection $resource) {
            // 從 Inertia 視圖中獲取所有傳遞的屬性
            $props = $this->toArray()['props'];

            // 將分頁資源編譯為陣列格式
            $compiledResource = $resource->response()->getData(true);

            // 斷言：確保指定的 key 存在於傳遞給 Inertia 的屬性中
            \PHPUnit\Framework\Assert::assertArrayHasKey($key, $props, "Key {$key} not passed as a property to Inertia");

            // 驗證分頁資源應該具有的必要結構：data（資料）、links（分頁連結）、meta（元資料）
            foreach (['data', 'links', 'meta'] as $propKey) {
                \PHPUnit\Framework\Assert::assertArrayHasKey($propKey, $props[$key], "The paginated resource for key {$key} does not have the expected structure.");
            }

            // 斷言：確保資源的資料部分（data key）與預期的資源完全匹配
            // 注意：分頁資源的實際資料存儲在 'data' 鍵下，而不是頂層
            \PHPUnit\Framework\Assert::assertEquals(
                $compiledResource,
                $props[$key]['data'],
                "The resource for key {$key} does not match the expected resource."
            );

            // 回傳 $this 以支援鏈式呼叫
            return $this;
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
