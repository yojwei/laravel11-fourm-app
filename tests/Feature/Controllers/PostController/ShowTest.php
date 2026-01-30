<?php

namespace Tests\Feature\Controllers\PostController;

use App\Http\Resources\CommentResource;
use App\Http\Resources\PostResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * 測試顯示帖子的控制器
 */
class ShowTest extends TestCase
{
    /**
     * 應該返回正確的組件
     */
    public function test_should_return_the_correct_component()
    {
        $this->withoutExceptionHandling();

        $post = Post::factory()->create();
        $post->load(['user', 'topic']);

        $response = $this->get($post->showRoute());
        $response->assertHasResource('post', PostResource::make($post));
    }

    /**
     * 應該將評論傳遞給視圖
     */
    public function test_should_passes_comments_to_view()
    {
        $this->withoutExceptionHandling();

        $posts = Post::factory()->create();

        $comments = Comment::factory(2)
            ->for($posts)
            ->create();

        $comments->load('user');

        $response = $this->get($posts->showRoute());
        $response->assertHasPaginatedResource('comments', CommentResource::collection($comments->reverse()));
    }

    /** 重定向舊的或不正確的 URL */
    #[DataProvider('slugProvider')]
    public function test_will_redirect_if_the_slug_is_incorrect($slug)
    {
        $this->withoutExceptionHandling();

        $post = Post::factory()->create(['title' => 'Hello world']);

        $this->get(route('posts.show', [$post, $slug, 'page' => 2]))
            ->assertRedirect($post->showRoute(['page' => 2]));
    }

    /** body provider */
    public static function slugProvider()
    {
        return [
            ['foo-bar'],
            ['hello'],    // 原本使用 contains 時，會產生錯誤
        ];
    }
}
