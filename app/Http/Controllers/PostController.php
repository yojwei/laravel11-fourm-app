<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\TopicResource;
use App\Models\Post;
use App\Models\Topic;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * 顯示所有貼文列表
     *
     * @return \Inertia\Response
     */
    public function index(?Topic $topic = null)
    {
        Gate::authorize('viewAny', Post::class);

        $posts = Post::with(['user', 'topic'])
            ->when($topic, fn(Builder $query) => $query->whereBelongsTo($topic))
            ->when(
                request()->query('search'),
                fn(Builder $query) => $query->whereAny(['title', 'body'], 'like', '%' . request()->query('search') . '%')
            ) // /posts?search={keyword}
            ->latest()
            ->latest('id')
            ->paginate();

        return inertia('Post/Index', [
            'posts' => fn() => PostResource::collection($posts),
            'topics' => fn() => TopicResource::collection(Topic::all()),
            'selectedTopic' => fn() => $topic ? TopicResource::make($topic) : null,
            'query' => request()->query('search', ''),
        ]);
    }

    /**
     * 顯示單個貼文詳細頁面
     *
     * @param Post $post 要顯示的貼文實例
     * @return \Inertia\Response
     */
    public function show(Post $post)
    {
        Gate::authorize('view', $post);

        $currentPath = request()->path();

        // 檢查URL是否包含正確的slug，若不正確則重定向
        if (!Str::endsWith($post->showRoute(), $currentPath)) {
            return redirect($post->showRoute(request()->query()), 301); // 301 代表永久重定向
        }

        $post->load(['user', 'topic']);

        return inertia('Post/Show', [
            'post' => fn() => PostResource::make($post)->withLikePermission(),
            'comments' => function () use ($post) {
                $resource = CommentResource::collection($post->comments()->with('user')->latest()->latest('id')->paginate(10));
                $resource->collection->each->withLikePermission();

                return $resource;
            },
        ]);
    }

    /**
     * 保存新建的貼文
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        // 驗證輸入資料
        $data = request()->validate([
            'title' => 'required|string|max:120|min:8',
            'body' => 'required|string|max:10000|min:20',
            'topic_id' => 'required|exists:topics,id',
        ]);

        // 建立新貼文
        $post = Post::create([
            ...$data,
            'user_id' => request()->user()->id,
        ]);

        // 重定向到新貼文的詳細頁面
        return redirect($post->showRoute())->banner('Post created successfully!');
    }

    /**
     * 顯示建立貼文的表單頁面
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        Gate::authorize('create', Post::class);

        return inertia('Post/Create', [
            'topics' => fn() => TopicResource::collection(Topic::all()),
        ]);
    }
}
