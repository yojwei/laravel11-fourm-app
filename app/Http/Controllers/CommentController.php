<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    public function store(Request $request, Post $post)
    {
        $data = request()->validate(['body' => ['required', 'string', 'max:2500'],]);

        Comment::create([
            ...$data,
            'post_id' => $post->id,
            'user_id' => $request->user()->id,
        ]);

        return to_route('posts.show', $post);
    }

    public function update(Comment $comment)
    {
        Gate::authorize('update', $comment);

        $comment->update(
            request()->validate([
                'body' => ['required', 'string', 'max:2500'],
            ])
        );

        return to_route('posts.show', [
            'post' => $comment->post_id,
            'page' => request()->query('page')
        ]);
    }

    public function destroy(Comment $comment)
    {
        Gate::authorize('delete', $comment);

        $comment->delete();

        return to_route('posts.show', [
            'post' => $comment->post_id,
            'page' => request()->query('page')
        ]);
    }
}
