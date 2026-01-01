<?php

namespace Tests\Feature\Controllers\CommentController;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
    }

    public function test_if_unauthenticated_user_can_access_delete_route()
    {
        $this->withExceptionHandling();

        $comment = Comment::factory()->create();

        $this->delete(route('comments.destroy', $comment))
            ->assertRedirect(route('login'));
    }

    public function test_user_can_delete_own_comment()
    {
        $user = $this->signInAsUser();

        $comment = Comment::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->delete(route('comments.destroy', $comment));

        $this->assertDatabaseMissing('comments', [
            'id' => $comment->id,
        ]);
    }

    public function test_redirect_to_post_show_after_deleting_comment()
    {
        $user = $this->signInAsUser();

        $comment = Comment::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->delete(route('comments.destroy', $comment))
            ->assertRedirect(route('posts.show', $comment->post_id));
    }

    public function test_prevent_deleting_other_users_comments()
    {
        $this->signInAsUser();

        $comment = Comment::factory()->create();

        $this->withExceptionHandling();

        $this->delete(route('comments.destroy', $comment))
            ->assertStatus(403);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
        ]);
    }
}
