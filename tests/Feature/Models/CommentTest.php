<?php

namespace Tests\Feature\Models;

use Tests\TestCase;

class CommentTest extends TestCase
{
    /** generate html from body */
    public function test_generate_html_from_body()
    {
        $this->withoutExceptionHandling();

        $comment = \App\Models\Comment::factory()->make([
            'body' => "This is a *sample* comment with **markdown**.",
        ]);

        $comment->save();

        $this->assertEquals(
            str($comment->body)->markdown(),
            $comment->html
        );
    }
}
