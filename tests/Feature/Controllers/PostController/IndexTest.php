<?php

namespace Tests\Feature\Controllers\PostController;

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
        $response = $this->get(route('posts.index'));

        $response->assertInertia(
            fn($inertia) => $inertia
                ->has('posts')
        );
    }
}
