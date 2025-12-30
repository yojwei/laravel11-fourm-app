<?php

namespace Tests\Feature\Controllers\PostController;

use App\Http\Resources\CommentResource;
use App\Http\Resources\PostResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ShowTest extends TestCase
{
    public function test_should_return_the_correct_component()
    {
        $this->withoutExceptionHandling();

        $post = Post::factory()->create();
        $post->load('user');

        $response = $this->get(route('posts.show', $post->id));
        $response->assertHasResource('post', PostResource::make($post));
    }

    public function test_should_passes_comments_to_view()
    {
        $this->withoutExceptionHandling();

        $posts = Post::factory()->create();

        $comments = Comment::factory(2)
            ->for($posts)
            ->create();

        $comments->load('user');

        $response = $this->get(route('posts.show', $posts->id));
        $response->assertHasPaginatedResource('comments', CommentResource::collection($comments->reverse()));
    }
}
