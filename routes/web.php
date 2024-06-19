<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RestTestController;
use App\Http\Controllers\DiggingDeeperController;
use App\Http\Controllers\Api\Blog\PostControllerApi;
use App\Http\Controllers\Api\Blog\CategoryControllerApi;
Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
Route::resource('rest', RestTestController::class)->names('restTest');
Route::group([ 'namespace' => 'App\Http\Controllers\Blog', 'prefix' => 'blog'], function () {
    Route::resource('posts', PostController::class)->names('blog.posts');
    
});
Route::group(['prefix' => 'digging_deeper'], function () {

    Route::get('collections', [App\Http\Controllers\DiggingDeeperController::class, 'collections'])

        ->name('digging_deeper.collections');
        Route::get('process-video', 'App\Http\Controllers\DiggingDeeperController@processVideo')
        ->name('digging_deeper.processVideo');
        
        Route::get('prepare-catalog', 'App\Http\Controllers\DiggingDeeperController@prepareCatalog')
        ->name('digging_deeper.prepareCatalog'); 
    
});
//Адмінка
$groupData = [
    'namespace' => 'App\Http\Controllers\Blog\Admin',
    'prefix' => 'admin/blog',
];
Route::group($groupData, function () {
    //BlogCategory
    $methods = ['index','edit','store','update','create',];
    Route::resource('categories', CategoryController::class)
    ->only($methods)
    ->names('blog.admin.categories'); 
    
    //BlogPost
    Route::resource('posts', PostController::class)
    ->except(['show'])                               //не робити маршрут для метода show
    ->names('blog.admin.posts');
 });
 Route::get('api/blog/posts', [\App\Http\Controllers\Api\Blog\PostControllerApi::class, 'index']);
 Route::get('api/blog/posts/{id}', [\App\Http\Controllers\Api\Blog\PostControllerApi::class, 'show']);




 Route::get('api/categories', [\App\Http\Controllers\Api\Blog\CategoryControllerApi::class, 'index']);
 Route::get('api/categories/{id}', [\App\Http\Controllers\Api\Blog\CategoryControllerApi::class, 'show']);
 Route::post('/posts', [\App\Http\Controllers\Api\Blog\PostControllerApi::class, 'store']);




 //Route::post('api/categories', [\App\Http\Controllers\Api\Blog\CategoryControllerApi::class, 'store']);
 Route::post('/categories', [\App\Http\Controllers\Api\Blog\CategoryControllerApi::class, 'store']);
 Route::put('api/categories/update/{id}', [\App\Http\Controllers\Api\Blog\CategoryControllerApi::class, 'update']);
 Route::get('api/categories/edit/{id}', [\App\Http\Controllers\Admin\Blog\CategoryController::class, 'edit']);
 Route::delete('api/categories/delete/{id}', [\App\Http\Controllers\Api\Blog\CategoryControllerApi::class, 'destroy']);

 Route::prefix('api')->group(function () {
    Route::resource('categories', CategoryControllerApi::class);

    Route::post('api/categories', [\App\Http\Controllers\Api\Blog\CategoryControllerApi::class, 'create']);
    
});

