<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

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

    public function destroy(Comment $comment)
    {
        if (request()->user()->id !== $comment->user_id) {
            abort(403);
        }

        $comment->delete();

        return to_route('posts.show', $comment->post_id);
    }
}
