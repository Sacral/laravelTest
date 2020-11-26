<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Article;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\TripController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//Route::get('/articles', 'Http/Controllers/ArticleController@index');
Route::get('/articles', [ArticleController::class, 'index']);

Route::get('/trip', function () {
    $headers = array('Content-Type' => 'application/json; charset=utf-8');
    $users = DB::table(‘users’)->get();
    return Response::json($users, 200, $headers, JSON_UNESCAPED_UNICODE);
    });



Route::get('/trip', [TripController::class, 'index']);
//Route::get('/articles/{id}', 'ArticleController@show');
Route::get('/articles/{id}', [ArticleController::class, 'show']);
Route::post('articles', 'ArticleController@store');
Route::put('/articles/{id}', 'ArticleController@update');
Route::delete('/articles/{id}', 'ArticleController@delete');
