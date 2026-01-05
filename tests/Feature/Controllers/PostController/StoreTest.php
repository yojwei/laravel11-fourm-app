<?php

namespace Tests\Feature\Controllers\PostController;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StoreTest extends TestCase
{
    /** requires authentication */
    public function test_should_require_authentication()
    {
        $response = $this->post(route('posts.store'), [
            'title' => 'Test Post',
            'content' => 'This is a test post content.',
        ]);

        $response->assertRedirect(route('login'));
    }
}
