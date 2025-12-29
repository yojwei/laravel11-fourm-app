<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Testing\TestResponse;
use Inertia\Testing\AssertableInertia;

class TestingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if (! $this->app->runningUnitTests()) {
            return;
        }

        AssertableInertia::macro('hasResource', function (string $key, JsonResource $resource) {
            $props = $this->toArray()['props'];
            $compiledResource = $resource->response()->getData(true);

            // Assert the key exists
            \PHPUnit\Framework\Assert::assertArrayHasKey($key, $props, "Key {$key} not passed as a property to Inertia");

            // Assert the resource matches
            \PHPUnit\Framework\Assert::assertEquals(
                $compiledResource,
                $props[$key],
                "The resource for key {$key} does not match the expected resource."
            );

            return $this;
        });

        AssertableInertia::macro('hasPaginatedResource', function (string $key, ResourceCollection $resource) {
            $props = $this->toArray()['props'];
            $compiledResource = $resource->response()->getData(true);

            // Assert the key exists
            \PHPUnit\Framework\Assert::assertArrayHasKey($key, $props, "Key {$key} not passed as a property to Inertia");

            // paginated resource should have data, links, meta
            foreach (['data', 'links', 'meta'] as $propKey) {
                \PHPUnit\Framework\Assert::assertArrayHasKey($propKey, $props[$key], "The paginated resource for key {$key} does not have the expected structure.");
            }

            // Assert the resource matches
            \PHPUnit\Framework\Assert::assertEquals(
                $compiledResource,
                $props[$key]['data'],
                "The resource for key {$key} does not match the expected resource."
            );

            return $this;
        });

        TestResponse::macro('assertHasResource', function (string $key, JsonResource $resource) {
            return $this->assertInertia(fn(AssertableInertia $page) => $page->hasResource($key, $resource));
        });

        TestResponse::macro('assertHasPaginatedResource', function (string $key, ResourceCollection $resource) {
            return $this->assertInertia(fn(AssertableInertia $page) => $page->hasPaginatedResource($key, $resource));
        });
    }
}
