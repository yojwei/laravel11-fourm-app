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

        // 將第一個用戶更新為 Test User
        $users->first()->update([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // 建立 200 篇貼文，並將其關聯到現有的用戶
        $posts = Post::factory(200)
            ->recycle($users)
            ->create();

        // 建立 500 則評論，並將其關聯到現有的用戶和貼文
        Comment::factory(500)
            ->recycle($users)
            ->recycle($posts)
            ->create();
    }
}
