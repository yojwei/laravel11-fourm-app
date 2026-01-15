<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;

class PostFixtures
{

    private static ?Collection $fixtures = null;

    /**
     * 載入 fixture 文章檔案內容，並快取於靜態屬性。
     *
     * @return Collection 文章內容集合
     */
    public static function getFixtures(): Collection
    {
        return self::$fixtures ??= collect(File::files(database_path('factories/fixtures/posts')))
            ->map(fn(SplFileInfo $file) => $file->getContents())->map(fn(string $contents) => str($contents)->explode("\n", 2))
            ->map(fn(Collection $parts) => [
                'title' => str($parts[0])->trim()->after('# '),
                'body' => str($parts[1] ?? '')->trim(),
            ]);
    }
}
