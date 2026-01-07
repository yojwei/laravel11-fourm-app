<?php

namespace Tests\Feature\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostTest extends TestCase
{
    /** use title case for post */
    public function test_title_case_for_post()
    {
        $this->withoutExceptionHandling();

        $post = \App\Models\Post::factory()->create([
            'title' => 'this is a sample post title',
        ]);

        $this->assertEquals('This Is A Sample Post Title', $post->title);
    }
}
