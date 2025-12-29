<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{

    public function index()
    {
        return inertia('Post/Index', [
            'posts' => PostResource::collection(Post::latest()->latest('id')->paginate()),
        ]);
    }
}
