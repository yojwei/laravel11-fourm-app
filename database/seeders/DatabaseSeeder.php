<?php

namespace Database\Seeders;

use App\Models\Comment;
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
        // 建立 10 個測試用戶
        $users = User::factory(10)->create();
        // 建立 200 篇貼文，並將其關聯到現有的用戶
        $posts = Post::factory(200)
            ->recycle($users)
            ->create();

        // 建立 500 則評論，並將其關聯到現有的用戶和貼文
        Comment::class::factory(500)
            ->recycle($users)
            ->recycle($posts)
            ->create();

        // 建立測試用戶，擁有 50 篇貼文和 150 則評論
        User::factory()
            ->has(Post::factory(50))
            ->has(Comment::factory(150)->recycle($posts))
            ->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
    }
}
