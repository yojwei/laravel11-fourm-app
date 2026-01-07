<?php

namespace Tests\Feature\Controllers\PostController;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateTest extends TestCase
{
    /** require authentication */
    public function test_should_require_authentication()
    {
        $this->get(route('posts.create'))
            ->assertRedirect(route('login'));
    }

    public function test_should_return_the_correct_component()
    {
        $this->signInAsUser();

        $response = $this->get(route('posts.create'));

        $response->assertInertia(
            fn($inertia) => $inertia
                ->component('Post/Create', true)
        );
    }
}
