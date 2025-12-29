<?php

namespace Tests\Feature\Controllers\PostController;

use App\Http\Resources\PostResource;
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
}
