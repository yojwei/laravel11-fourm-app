<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(TopicSeeder::class);
        $topics = \App\Models\Topic::all();

        // 建立 10 個測試用戶
        $users = User::factory(10)->create();

        // 建立 200 篇貼文，並將其關聯到現有的用戶
        $posts = Post::factory(200)
            ->withFixture()
            ->has(Comment::factory(18)->recycle($users))
            ->recycle([$users, $topics])
            ->create();

        // 新增一個測試用戶
        User::factory()
            ->has(Post::factory(50)->recycle($topics)->withFixture())
            ->has(Comment::factory(200)->recycle($posts))
            ->has(Like::factory(100)->forEachSequence(
                ...$posts->random(100)->map(fn(Post $post) => ['likeable_id' => $post])
            ))      // recycle 貼文讚，因為隨機抽取的關係會造成重複的錯誤
            ->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
    }
}
