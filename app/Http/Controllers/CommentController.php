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

        return redirect($post->showRoute())
            ->banner('Comment added！');
    }

    public function update(Comment $comment)
    {
        Gate::authorize('update', $comment);

        $comment->update(
            request()->validate([
                'body' => ['required', 'string', 'max:2500'],
            ])
        );

        return redirect($comment->post->showRoute([
            'post' => $comment->post_id,
            'page' => request()->query('page')
        ]))->banner('Comment updated！');
    }

    public function destroy(Comment $comment)
    {
        Gate::authorize('delete', $comment);

        $comment->delete();

        return redirect($comment->post->showRoute([
            'post' => $comment->post_id,
            'page' => request()->query('page')
        ]))->banner('Comment deleted！');;
    }
}
