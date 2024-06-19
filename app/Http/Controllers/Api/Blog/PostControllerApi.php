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
    
    public function create(BlogPostCreateRequest $request)
    {
        $data = $request->input(); //отримаємо масив даних, які надійшли з форми

        $item = (new BlogPost())->create($data); //створюємо об'єкт і додаємо в БД

        if ($item) {
            $job = new BlogPostAfterCreateJob($item);
            $this->dispatch($job);
            return response()->json(['success' => 'Post created successfully'], 200);
        } else {
            response()->json(['error' => 'Post not found'], 404);
        }
    }






    public function store(BlogPostCreateRequest $request)
    {
        $validated = $request->validated();

        // Create new blog post
        $post = new BlogPost();
        $post->title = $validated['title'];
        $post->slug = $validated['slug'];
        $post->category_id = $validated['category_id'];
        $post->content_raw = $validated['content_raw'];
        $post->save();

        return response()->json($post, 201);
    }
    public function update(BlogPostUpdateRequest $request, string $id)
    {
        $item = $this->blogPostRepository->getEdit($id);
        if (empty($item)) { //якщо ід не знайдено
            return response()->json(['error' => 'Post not found'], 404);
        }

        $data = $request->all(); //отримаємо масив даних, які надійшли з форми


        $result = $item->update($data); //оновлюємо дані об'єкта і зберігаємо в БД

        if ($result) {
            return response()->json(['success' => 'Post deleted successfully'], 200);
        } else {
            return response()->json(['error' => "The update could not be completed due to a conflict with the current state of the resource."], 409);
        }
    }
    public function destroy($id)
{
    $post = BlogPost::find($id);
    if (empty($post)) { // Якщо пост не знайдено
        return response()->json(['error' => 'Post not found'], 404);
    }

    $result = $post->delete(); // Видаляємо пост

    if ($result) {
        return response()->json(['success' => 'Post deleted successfully'], 200);
    } else {
        return response()->json(['error' => 'Error deleting post'], 500);
    }
}
}



