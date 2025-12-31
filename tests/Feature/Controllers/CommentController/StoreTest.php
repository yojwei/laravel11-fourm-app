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

    /**
     * @dataProvider invalidBodyProvider
     */
    public function test_it_requires_a_valid_body($value): void
    {
        $this->withExceptionHandling();
        $post = Post::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post(route('posts.comments.store', $post), [
                'body' => $value,
            ])
            ->assertInvalid('body');
    }


    public static function invalidBodyProvider(): array
    {
        return [
            'body 是 null' => [null],
            'body 是 整數' => [1],
            'body 是 浮點數' => [1.5],
            'body 是 布林值' => [true],
            'body 超過 2500 字' => [str_repeat('a', 2501)],
        ];
    }
}
