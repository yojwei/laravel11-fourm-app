<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentResource;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;

class PostController extends Controller
{

    public function index()
    {
        Gate::authorize('viewAny', Post::class);

        return inertia('Post/Index', [
            'posts' => fn() => PostResource::collection(Post::with('user')->latest()->latest('id')->paginate()),
        ]);
    }

    public function show(Post $post)
    {
        Gate::authorize('view', $post);

        $post->load('user');

        return inertia('Post/Show', [
            'post' => fn() => PostResource::make($post),
            'comments' => fn() => CommentResource::collection($post->comments()->with('user')->latest()->latest('id')->paginate(10)),
        ]);
    }

    public function store()
    {
        $data = request()->validate([
            'title' => 'required|string|max:120|min:8',
            'body' => 'required|string|max:10000|min:20',
        ]);

        $post = Post::create([
            ...$data,
            'user_id' => request()->user()->id,
        ]);


        return to_route('posts.show', $post->id);
    }

    public function create()
    {
        Gate::authorize('create', Post::class);

        return inertia('Post/Create');
    }
}
