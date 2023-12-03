<?php

use App\Http\Controllers\GithubController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\RepositoryController;
use Illuminate\Support\Facades\Redirect;
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
    return Redirect::route('login');
});

Route::get('/register', function () {
    return Redirect::route('login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/home', [MainController::class, 'index'])->name('home');
    Route::get('/dashboard', [MainController::class, 'dashboard'])->name('dashboard');
    Route::get('/subscribers', [SubscriberController::class, 'index'])->name('subscribers');

    Route::get('/repositories/request', [RepositoryController::class, 'getRequestNew'])->name('repositories-request-get');
    Route::post('/repositories/create', [RepositoryController::class, 'create'])->name('repositories-create');
    Route::get('/repositories', [RepositoryController::class, 'index'])->name('repositories-index');

    Route::get('/github/repositories', [GithubController::class, 'getRepositories'])->name('github-repositories-get'); // TODO: LEFT

});


Route::get('/auth/github/callback', [GithubController::class, 'callback'])->name('callback');
Route::get('/auth/github', [GithubController::class, 'redirect'])->name('redirect');