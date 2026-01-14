<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    private static ?Collection $fixtures = null;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' =>  \App\Models\User::factory(),
            'title' => Str(fake()->sentence())->title()->beforeLast('.'),
            'body' => Collection::times(4, fn() => fake()->realText(1250))->join(PHP_EOL . PHP_EOL),
        ];
    }

    public function withFixture(): static
    {
        // 將所有 fixture 文章內容轉為 sequence，
        // 每篇文章以第一行為標題（去除 # 及前後空白），其餘為內文
        $posts = static::getFixtures()
            ->map(fn(string $contents) => str($contents)->explode("\n", 2))
            ->map(fn(Collection $parts) => [
                'title' => str($parts[0])->trim()->after('# '),
                'body' => str($parts[1] ?? '')->trim(),
            ]);

        // 依序產生 fixture 內容
        return $this->sequence(...$posts);
    }

    /**
     * 載入 fixture 文章檔案內容，並快取於靜態屬性。
     *
     * @return Collection 文章內容集合
     */
    private static function getFixtures(): Collection
    {
        // 若 $fixtures 尚未載入，則從指定資料夾讀取所有檔案
        // File::files 取得所有檔案（回傳 SplFileInfo 陣列）
        // collect() 將其轉為 Collection 物件
        // map() 針對每個檔案物件，呼叫 getContents() 取得檔案內容
        // 最終得到一個「檔案內容」的集合，並快取於 $fixtures
        return self::$fixtures ??= collect(File::files(database_path('factories/fixtures/posts')))
            ->map(fn(SplFileInfo $file) => $file->getContents());
    }
}
