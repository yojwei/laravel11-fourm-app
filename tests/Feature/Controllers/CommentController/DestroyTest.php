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
            ->assertRedirect($comment->post->showRoute());
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

    public function test_prevent_deleting_comments_over_a_hour()
    {
        $this->freezeTime();
        $user = $this->signInAsUser();
        $comment = Comment::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->withExceptionHandling();

        $this->travel(1)->hours();

        $this->delete(route('comments.destroy', $comment))
            ->assertStatus(403);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
        ]);
    }

    public function test_redirects_to_post_show_with_page_query()
    {
        $user = $this->signInAsUser();

        $comment = Comment::factory()->create([
            'user_id' => $user->id,
        ]);

        $page = 2;

        $this->delete(route('comments.destroy', ['comment' => $comment->id, 'page' => $page]))
            ->assertRedirect($comment->post->showRoute(['page' => $page]));
    }
}
