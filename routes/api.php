<?php

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
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

//Route::middleware('jwt.auth')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::group(['middleware' => ['jwt.auth']], function () {
    Route::post('/comment/send', 'CommentsController@send')->name('api.comments.send');
    Route::post('/logout', function (){
       return auth()->logout();
    });
});

Route::get("/comments", 'CommentsController@all')->name('api.comments');
Route::get("/comments/{comment}/get", 'CommentsController@get')->name('api.comments.get');

