<?php

namespace Tests\Feature\Controllers\CommentController;

use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EditTest extends TestCase
{
    # requeires authentication to access edit route
    public function test_if_unauthenticated_user_can_access_edit_route()
    {
        $this->withExceptionHandling();

        $comment = Comment::factory()->create();

        $this->get(route('comments.edit', $comment))
            ->assertRedirect(route('login'));
    }

    # can update a comment
    public function test_user_can_update_own_comment()
    {
        $user = $this->signInAsUser();

        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'body' => 'Old comment body',
        ]);

        $this->put(route('comments.update', $comment), [
            'body' => 'Updated comment body',
        ]);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'body' => 'Updated comment body',
        ]);
    }

    # redirect to post show after updating comment
    public function test_user_is_redirected_to_post_show_after_updating_comment()
    {
        $user = $this->signInAsUser();

        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'body' => 'Old comment body',
        ]);

        $response = $this->put(route('comments.update', $comment), [
            'body' => 'Updated comment body',
        ]);

        $response->assertRedirect($comment->post->showRoute());
    }

    # redirect to the current page of comments after updating comment
    public function test_user_is_redirected_to_current_page_after_updating_comment()
    {
        $user = $this->signInAsUser();

        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'body' => 'Old comment body',
        ]);

        $response = $this->put(route('comments.update', $comment) . '?page=2', [
            'body' => 'Updated comment body',
        ]);

        $response->assertRedirect($comment->post->showRoute(['page' => 2]));
    }


    # cannot update a comment from other users
    public function test_user_cannot_update_other_users_comment()
    {
        $this->signInAsUser();

        $comment = Comment::factory()->create([
            'body' => 'Old comment body',
        ]);

        $this->withExceptionHandling();

        $this->put(route('comments.update', $comment), [
            'body' => 'Updated comment body',
        ])->assertStatus(403);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'body' => 'Old comment body',
        ]);
    }

    # reqires a valid body
    public function test_update_comment_requires_valid_body()
    {
        $user = $this->signInAsUser();

        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'body' => 'Old comment body',
        ]);

        $this->withExceptionHandling();

        $this->put(route('comments.update', $comment), [
            'body' => '',
        ])->assertSessionHasErrors('body');

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'body' => 'Old comment body',
        ]);
    }
}
