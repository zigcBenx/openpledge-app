<?php

use App\Http\Controllers\CampaignController;
use App\Http\Controllers\GithubController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RepositoryController;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/home', [MainController::class, 'index'])->name('home');
    Route::get('/dashboard', [MainController::class, 'dashboard'])->name('dashboard');
    Route::get('/subscribers', [SubscriberController::class, 'index'])->name('subscribers');

    Route::get('/discover/issues', [MainController::class, 'discoverIssues'])->name('discover.issues');



    // TODO: this route is for displaying request view -> which will be removed
    // since whenever user comes from github repo to openpledge, repos should be automatically created
    // hwoever this could be used if user searched repos on open pledge and cant find it's own
    // but mybe it would be better when 0 results to request all github repos with certain filters
    // and if found automatically add and show index page...
    Route::get('/repositories/request', [RepositoryController::class, 'getRequestNew'])->name('repositories-request-get');

    Route::post('/repositories/connect', [RepositoryController::class, 'connect'])->name('repositories.connect');
    
    
    Route::get('/repositories/{githubUser}/{repository}', [RepositoryController::class, 'show'])->name('repositories.show');


    Route::resource('repositories', RepositoryController::class)->only('index', 'store');
    Route::resource('issues', IssueController::class)->only('index', 'show', 'store');
    Route::resource('campaigns', CampaignController::class);
    Route::resource('donations', DonationController::class)->only('index', 'show', 'store');


    Route::get('issues/{issue}/donations', [IssueController::class, 'donations'])->name('issues.donations');

    Route::get('/github/repositories', [GithubController::class, 'getRepositories'])->name('github-repositories-get');
    Route::get('/github/issues', [GithubController::class, 'getIssues'])->name('github-issues-get');

    Route::post('/get-payment-intent', [PaymentController::class, 'getPaymentIntent'])->name('get-payment-intent');


    // override of profile route
    Route::get('/user/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/user/profile/settings', [ProfileController::class, 'settings'])->name('profile.settings');

});

Route::get('/auth/github/callback', [GithubController::class, 'callback'])->name('callback');
Route::get('/auth/github', [GithubController::class, 'redirect'])->name('redirect');

Route::get('/unsubscribe-user', [SubscriberController::class, 'unsubscribe'])->name('unsubscribe');



Route::get('/{any}', function () {
    return Inertia::render('Error');
})->where('any', '.*');