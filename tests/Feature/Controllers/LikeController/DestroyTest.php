<?php

namespace Tests\Feature\Controllers\LikeController;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class DestroyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
    }

    public function test_if_unauthenticated_user_can_access_destroy_route()
    {
        $this->withExceptionHandling();

        $post = Post::factory()->create();

        $this->post(route('likes.destroy', ['post', $post->id]))
            ->assertRedirect(route('login'));
    }

    #[DataProvider('modelProvider')]
    public function test_if_allowed_to_unlike_a_likeable_model($modelClass)
    {
        $this->withExceptionHandling();
        $model = $modelClass::factory()->create();
        $user = $this->signInAsUser();

        Like::factory()->create([
            'user_id' => $user->id,
            'likeable_id' => $model->id,
            'likeable_type' => $model->getMorphClass(),
        ]);

        $this->fromRoute('dashboard')
            ->delete(route('likes.destroy', [$model->getMorphClass(), $model->id]))
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'likeable_id' => $model->id,
            'likeable_type' => $model->getMorphClass(),
        ]);

        $this->assertEquals(0, $model->refresh()->likes_count);
    }

    public function test_if_allowed_to_destroy_a_likeable_model_only_once()
    {
        $this->withExceptionHandling();
        $model = Post::factory()->create();
        $user = $this->signInAsUser();

        Like::factory()->create([
            'user_id' => $user->id,
            'likeable_id' => $model->id,
            'likeable_type' => $model->getMorphClass(),
        ]);

        $this->delete(route('likes.destroy', [$model->getMorphClass(), $model->id]))
            ->assertRedirect();

        $this->delete(route('likes.destroy', [$model->getMorphClass(), $model->id]))
            ->assertForbidden();

        $this->assertDatabaseCount('likes', 0);
        $this->assertEquals(0, $model->refresh()->likes_count);
    }

    public function test_it_only_delete_supported_models()
    {
        $this->withExceptionHandling();
        $user = $this->signInAsUser();

        $this->post(route('likes.destroy', [$user->getMorphClass(), $user->id]))
            ->assertForbidden();
    }

    public function test_it_only_allows_valid_type()
    {
        $this->withExceptionHandling();
        $this->signInAsUser();

        $this->post(route('likes.destroy', ['invalid-type', 123]))
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
