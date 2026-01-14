<?php

namespace Tests\Feature\Controllers\PostController;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StoreTest extends TestCase
{
    protected $data = [
        'title' => 'Test Post',
        'body' => 'This is a test post content.',
    ];

    /** requires authentication */
    public function test_should_require_authentication()
    {
        $response = $this->post(route('posts.store'), $this->data);

        $response->assertRedirect(route('login'));
    }

    /** stores a post */
    public function test_should_store_post()
    {
        $user = $this->signInAsUser();

        $this->post(route('posts.store'), $this->data);

        $this->assertDatabaseHas('posts', [
            ...$this->data,
            'user_id' => $user->id,
        ]);
    }

    /** redirect to the post show page*/
    public function test_should_redirect_to_post_index_page_after_storing()
    {
        $this->signInAsUser();

        $this->post(route('posts.store'), $this->data)
            ->assertRedirect(Post::latest('id')->first()->showRoute());
    }

    /** require a valid title
     * @dataProvider titleProvider
     */
    public function test_should_require_a_valid_title($badTitle)
    {
        $this->signInAsUser();

        $response = $this->post(route('posts.store'), [
            'title' => $badTitle,
            'body' => 'This is a test post content.',
        ]);

        $response->assertSessionHasErrors('title');
    }

    /** title provider */
    public static function titleProvider()
    {
        return [
            [null],
            [true],
            [1],
            [1.5],
            [str_repeat('A', 121)], // assuming max length is 120
            [str_repeat('A', 6)]
        ];
    }

    /** require a valid body
     * @dataProvider bodyProvider
     */
    public function test_should_require_a_valid_body($badBody)
    {
        $this->signInAsUser();

        $response = $this->post(route('posts.store'), [
            'title' => 'Test Post',
            'body' => $badBody,
        ]);

        $response->assertSessionHasErrors('body');
    }

    /** body provider */
    public static function bodyProvider()
    {
        return [
            [null],
            [true],
            [1],
            [1.5],
            [str_repeat('A', 10_001)], // assuming max length is 10000
            [str_repeat('A', 10)]
        ];
    }
}
