<?php
namespace App\Http\Controllers\Api\Blog;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;


use App\Http\Controllers\Blog\BaseController;
use App\Http\Requests\BlogCategoryCreateRequest;
use App\Http\Requests\BlogCategoryUpdateRequest;

use App\Models\BlogPost;
use App\Repositories\BlogCategoryRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use function Laravel\Prompts\alert;
use function Pest\Laravel\json;
class CategoryControllerApi
{
    // public function index1()
    // {
    //     $categories = BlogCategory::all();
    //     return response()->json($categories);
    // }

    public function index()
    {
        $categories = BlogCategory::with('parentCategory')->get()->map(function ($category) {
            return [
                'id' => $category->id,
                'title' => $category->title,
                'slug' => $category->slug,
                'parent_id' => $category->parent_id,
                'description' => $category->description,
                'parent_title' => $category->parent_title, 
                'created_at' => $category->created_at,
                'updated_at' => $category->updated_at,
                'deleted_at' => $category->deleted_at,
            ];
        });

        return response()->json($categories);
    }
   
    // Отримати конкретну категорію за ID
    public function show($id)
    {
        $category = BlogCategory::findOrFail($id);
        return response()->json($category);
    }



    public function create1(BlogCategoryCreateRequest $request)
    {
        $data = $request->input(); //отримаємо масив даних, які надійшли з форми

        $item = (new BlogCategory())->create($data); //створюємо об'єкт і додаємо в БД
        return response()->json(['success' => 'Category created successfully', 'category' => $item], 201);
    }


    public function create2()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    /* 'title',
        'slug',
        'parent_id',
        'description',*/
    public function store(Request $request)
    {
        $newCategory = BlogCategory::with('parentCategory')->create([
            'title' => $request->title,
            'slug' => $request->slug,
            'parent_id' => $request->parent_id,
            'description' =>$request->description
        ]);
        if($newCategory){
            return response()->json([
                'status' => 200,
                'message' => "Category Created Successfully"
            ],200);
        }else{
            return response()->json([
                'status' => 500,
                'message' => "Something went wrong"
            ],500);
        }
    }
    public function create(BlogCategoryCreateRequest $request)
    {
        $data = $request->input(); //отримаємо масив даних, які надійшли з форми

        $item = (new BlogCategory())->create($data); //створюємо об'єкт і додаємо в БД
        return response()->json(['success' => 'Category created successfully', 'category' => $item], 201);
    }

    public function update(BlogCategoryUpdateRequest $request, $id)
    {
        $item = $this->blogCategoryRepository->getEdit($id); //BlogCategory::find($id);
        if (empty($item)) { //якщо ід не знайдено
            return back() //redirect back
            ->withErrors(['msg' => "Запис id=[{$id}] не знайдено"]) //видати помилку
            ->withInput(); //повернути дані
        }

        $data = $request->all(); //отримаємо масив даних, які надійшли з форми

        $result = $item->update($data);  //оновлюємо дані об'єкта і зберігаємо в БД

        if ($result) {
            return response()->json(['success' => 'Category updated successfully'], 200);
        } else {
            return response()->json(['error' => "The update could not be completed due to a conflict with the current state of the resource."], 409);
        }
    }
    public function destroy($id)
{
    $category = BlogCategory::find($id);

    if (!$category) {
        return response()->json(['error' => "Category not found"], 404);
    }

    $result = $category->delete();

    if ($result) {
        return response()->json(['success' => "Category deleted successfully"], 200);
    } else {
        return response()->json(['error' => "Failed to delete the category"], 500);
    }
}
}