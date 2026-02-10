<?php

namespace Tests\Feature\Controllers\LikeController;

use App\Models\Comment;
use App\Models\Post;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class StoreTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
    }

    public function test_if_unauthenticated_user_can_access_store_route()
    {
        $this->withExceptionHandling();

        $post = Post::factory()->create();

        $this->post(route('likes.store', ['post', $post->id]))
            ->assertRedirect(route('login'));
    }

    #[DataProvider('modelProvider')]
    public function test_if_allowed_to_like_a_likeable_model($modelClass)
    {
        $model = $modelClass::factory()->create();
        $user = $this->signInAsUser();

        $this->fromRoute('dashboard')
            ->post(route('likes.store', [$model->getMorphClass(), $model->id]))
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'likeable_id' => $model->id,
            'likeable_type' => $model->getMorphClass(),
        ]);

        $this->assertEquals(1, $model->refresh()->likes_count);
    }

    public function test_if_allowed_to_like_a_likeable_model_only_once()
    {
        $model = Post::factory()->create();
        $this->signInAsUser();
        $this->withExceptionHandling();

        $this->post(route('likes.store', [$model->getMorphClass(), $model->id]))
            ->assertRedirect();

        $this->post(route('likes.store', [$model->getMorphClass(), $model->id]))
            ->assertForbidden();

        $this->assertDatabaseCount('likes', 1);
        $this->assertEquals(1, $model->refresh()->likes_count);
    }

    public function test_it_only_liking_supported_models()
    {
        $user = $this->signInAsUser();
        $this->withExceptionHandling();

        $this->post(route('likes.store', [$user->getMorphClass(), $user->id]))
            ->assertForbidden();
    }

    public function test_it_only_allows_valid_type()
    {
        $user = $this->signInAsUser();
        $this->withExceptionHandling();

        $this->post(route('likes.store', ['invalid-type', 123]))
            ->assertNotFound();
    }

    public static function modelProvider()
    {
        return [
            'post' => [Post::class],
            'comment' => [Comment::class]
        ];
    }
}
