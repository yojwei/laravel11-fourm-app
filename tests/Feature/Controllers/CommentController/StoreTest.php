<?php

namespace Tests\Feature\Controllers\CommentController;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StoreTest extends TestCase
{
    public function test_can_store_a_comment()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $post = Post::factory()->create();

        $this->actingAs($user)->post(route('posts.comments.store', $post), [
            'body' => 'This is a comment',
        ]);

        $this->assertDatabaseHas('comments', [
            'body' => 'This is a comment',
            'post_id' => $post->id,
            'user_id' => $user->id,
        ]);
    }
}
