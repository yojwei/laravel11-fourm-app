<?php

namespace Tests\Feature\Controllers\PostController;

use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class IndexTest extends TestCase
{
    public function test_should_return_the_correct_component()
    {
        $response = $this->get(route('posts.index'));

        $response->assertInertia(
            fn($inertia) => $inertia
                ->component('Post/Index', true)
        );
    }

    public function test_should_passes_posts_to_view()
    {
        $posts = Post::factory(3)->create();

        $response = $this->get(route('posts.index'));
        $response->assertHasPaginatedResource('posts', PostResource::collection($posts->reverse()));
    }
}
