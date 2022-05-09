<?php

use Illuminate\Support\Facades\Route;

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

Route::post('/user', function ($id = 'user') {
    echo 'User'. $id;
});
Route::prefix('auth')->group(function (){
    Route::post('/registeragency','AuthController@registerAgency');
    Route::post('/registerclient','AuthController@registerClient');
    Route::post('/login','AuthController@login');
    Route::post('/updatebio','AuthController@updatebio')->middleware('auth:api');
    Route::get('/logout','AuthController@logout')->middleware('auth:api');
    Route::get('/user','AuthController@user')->middleware('auth:api');
    Route::post('/user/{id}','AuthController@userfinder')->middleware('auth:api');
}
);
Route::post('/newpost','PostController@new_post')->middleware('auth:api');
Route::post('/newtag','TagController@createTag')->middleware('auth:api');

Route::post('/comment','PostController@comment')->middleware('auth:api');
Route::post('/like/{postId}','PostController@like')->middleware('auth:api');
Route::get('/postcomments/{postId}','PostController@postComments')->middleware('auth:api');
Route::get('/postimages/{postId}','PostController@postImages')->middleware('auth:api');
Route::get('/postlikes/{postId}','PostController@postLikes')->middleware('auth:api');

Route::get('/posts','PostController@posts')->middleware('auth:api');
Route::post('/post/{postId}','PostController@post')->middleware('auth:api');
Route::put('/updatepost/{postId}','PostController@update')->middleware('auth:api');
Route::delete('/deletepost/{postId}','PostController@destroy')->middleware('auth:api');


Route::post('/search','searchController@search')->middleware('auth:api');

//
//Route::get('/postss', function() {
//    return new \App\Http\Resources\Post(\App\Post::with('comments')->get());
//});

//Route::get('/comments', function() {
//    return new \App\Http\Resources\Comment(\App\Comment::all());
//});
//
//Route::get('/likes', function() {
//    return new \App\Http\Resources\Like(\App\Like::all());
//});

