<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Post $post)
    {
        $comment = Comment::make(request()->all());

        $comment->user()->associate($request->user());
        $comment->post()->associate($post);
        $comment->save();

        return to_route('posts.show', $post);
    }
}
