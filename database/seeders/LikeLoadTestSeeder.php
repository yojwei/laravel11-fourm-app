<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use function Laravel\Prompts\progress;

class LikeLoadTestSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $post = Post::find(250);
        $likeableType = (new Post)->getMorphClass();
        $totalLikes = 500_000;
        $batchSize = 5_000;
        $batches = $totalLikes / $batchSize;

        $progress = progress('Creating likes for load testing...', $batches);
        $progress->start();

        for ($i = 0; $i < $batches; $i++) {
            $now = now();

            // 批次建立 users（raw insert，不經 Eloquent）
            $userRows = [];
            for ($j = 0; $j < $batchSize; $j++) {
                $userRows[] = [
                    'name' => 'Test User',
                    'email' => 'test' . Str::uuid() . '@example.com',
                    'password' => '$2y$12$examplehashedpassword000000000000000000000000000000',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('users')->insert($userRows);

            // 取得剛插入的 user IDs
            $lastId = (int) DB::getPdo()->lastInsertId();
            $userIds = range($lastId, $lastId + $batchSize - 1);

            // 批次建立 likes（raw insert）
            $likeRows = [];
            foreach ($userIds as $userId) {
                $likeRows[] = [
                    'user_id' => $userId,
                    'likeable_type' => $likeableType,
                    'likeable_id' => $post->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('likes')->insert($likeRows);

            $progress->advance();
        }

        $progress->finish();
        $post->increment('likes_count', 500_000);
    }
}
