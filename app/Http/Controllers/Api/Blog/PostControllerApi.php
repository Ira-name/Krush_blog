<?php
namespace App\Http\Controllers\Api\Blog;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
class PostControllerApi
{
    public function index()
    {
        $posts = BlogPost::with(['user', 'category'])->get();
        return $posts;
    }
    public function show($id)
    {
        $post = BlogPost::with(['user', 'category'])->findOrFail($id);
        return response()->json($post);
    }
}



