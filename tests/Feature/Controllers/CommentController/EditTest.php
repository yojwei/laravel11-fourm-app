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

    # redirect to post show after updating comment

    # redirect to the current page of comments after updating comment

    # cannot update a comment from other users

    # reqires a valid body
}
