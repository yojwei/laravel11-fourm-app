<?php

namespace Tests\Feature\Controllers\CommentController;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StoreTest extends TestCase
{
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_can_store_a_comment()
    {
        $post = Post::factory()->create();

        $this->post(route('posts.comments.store', $post), [
            'body' => 'This is a comment',
        ]);

        $this->assertDatabaseHas('comments', [
            'body' => 'This is a comment',
            'post_id' => $post->id,
            'user_id' => $this->user->id,
        ]);
    }

    public function test_store_should_redirect_to_show_page()
    {
        $post = Post::factory()->create();

        $this->post(route('posts.comments.store', $post), [
            'body' => 'This is a comment',
        ])
            ->assertRedirect(route('posts.show', $post));
    }
}
