<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('home');
});

// Route::get('/alert', function () {
//     return redirect()->route('home')->with('info', 'You have signed up!');
// });

Route::group (['middleware' => ['auth']], function () {
    Route::get('/search', [App\Http\Controllers\SearchController::class, 'getResults'])->name('search.results');
/**
* Profile
*/ 
    Route::get('/user/{username}', [App\Http\Controllers\ProfileController::class, 'getProfile'])->name('profile.index');
    Route::get('/profile/edit', [App\Http\Controllers\ProfileController::class, 'getEdit'])->name('profile.edit');
    Route::post('/profile/edit', [App\Http\Controllers\ProfileController::class, 'postEdit']);
/**
* Friends
*/ 
    Route::get('/friends', [App\Http\Controllers\FriendController::class, 'getIndex'])->name('friend.index');
    Route::get('/friends/add/{username}', [App\Http\Controllers\FriendController::class, 'getAdd'])->name('friend.add');
    Route::get('/friends/accept/{username}', [App\Http\Controllers\FriendController::class, 'getAccept'])->name('friend.accept');
/**
* Timeline
*/ 
    Route::post('/status', [App\Http\Controllers\StatusController::class, 'postStatus'])->name('status.post');
    Route::post('/status/{statusId}/reply', [App\Http\Controllers\StatusController::class, 'postReply'])->name('status.reply');
    Route::get('/status/{statusId}/like', [App\Http\Controllers\StatusController::class, 'getLike'])->name('status.like');

});



Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


