<?php

namespace Tests\Feature\Controllers\PostController;

use App\Http\Resources\PostResource;
use App\Http\Resources\TopicResource;
use App\Models\Post;
use App\Models\Topic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class IndexTest extends TestCase
{
    public function test_should_return_the_correct_component()
    {
        $response = $this->get(route('posts.index'));

        $response->assertInertia(
            fn($inertia) => $inertia
                ->component('Post/Index', true)
        );
    }

    public function test_should_passes_posts_to_view()
    {
        $posts = Post::factory(3)->create();
        $posts->load(['user', 'topic']);

        $response = $this->get(route('posts.index'));
        $response->assertHasPaginatedResource('posts', PostResource::collection($posts->reverse()));
    }

    public function test_should_passes_posts_filtered_to_a_topic()
    {
        $general = Topic::factory()->create();

        $posts = Post::factory(2)->for($general)->create();
        Post::factory(3)->create(); // 其他主題的貼文

        $posts->load(['user', 'topic']);

        $response = $this->get(route('posts.index', ['topic' => $general]));
        $response->assertHasPaginatedResource('posts', PostResource::collection($posts->reverse()));
    }

    public function test_should_passes_the_selected_topic_to_the_view()
    {
        $topic = Topic::factory()->create();

        $response = $this->get(route('posts.index', ['topic' => $topic]));
        $response->assertHasResource('selectedTopic', TopicResource::make($topic));
    }
}
