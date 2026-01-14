<?php

namespace Tests\Feature\Models;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class PostTest extends TestCase
{
    /**
     * 測試貼文標題是否會自動轉換為標題大小寫
     */
    public function test_title_case_for_post()
    {
        $this->withoutExceptionHandling();

        $post = Post::factory()->create([
            'title' => 'this is a sample post title',
        ]);

        $this->assertEquals('This Is A Sample Post Title', $post->title);
    }

    /**
     * 測試是否能產生指向顯示頁面的路由
     */
    public function test_can_generate_a_route_to_the_show_page()
    {
        $post = Post::factory()->create();

        $this->assertEquals(
            route('posts.show', [$post, Str::slug($post->title)]),
            $post->showRoute()
        );
    }

    /**
     * 測試是否能在顯示路由上產生額外的查詢參數
     */
    public function test_can_generate_additional_query_parameters_on_the_show_route()
    {
        $post = Post::factory()->create();

        $this->assertEquals(
            route('posts.show', [$post, Str::slug($post->title), 'page' => 2]),
            $post->showRoute(['page' => 2])
        );
    }

    /** generate html from body */
    public function test_generate_html_from_body()
    {
        $this->withoutExceptionHandling();

        $post = Post::factory()->make([
            'body' => "# Hello World\nThis is a **bold** statement.",
        ]);

        $post->save();

        $this->assertEquals(str($post->body)->markdown(), $post->html);
    }
}
