<?php

use App\Http\Controllers\CampaignController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\GithubController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RepositoryController;
use App\Http\Controllers\StripeConnectController;
use App\Http\Controllers\UserController;
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
    return Redirect::route('discover.issues');
});

Route::get('/discover/issues', [MainController::class, 'discoverIssues'])->name('discover.issues');
Route::get('issues/{issue}', [IssueController::class, 'show'])->name('issues.show');
Route::get('/repositories/{githubUser}/{repository}', [RepositoryController::class, 'show'])->name('repositories.show');

Route::post('/get-payment-intent', [PaymentController::class, 'getPaymentIntent'])->name('get-payment-intent');
Route::post('/payment-process', [PaymentController::class, 'processPayment'])->name('payment-process');
Route::post('issues/pledgeExternalIssue', [IssueController::class, 'pledgeExternalIssue'])->name('issues.pledge-external-issue');
Route::post('/user/user-feedback-submission', [UserController::class, 'handleUserFeedbackSubmission'])->name('user.feedback');

// top lists endpoints
Route::get('/trending-today-issues', [IssueController::class, 'getTrendingToday'])->name('trending-today-issues');
Route::get('/top-contributors', [Maincontroller::class, 'getTopContributors'])->name('top-contributors');
Route::get('/top-donors', [Maincontroller::class, 'getTopDonors'])->name('top-donors');
Route::get('/anonymous-donations', [Maincontroller::class, 'getAnonymousDonations'])->name('anonymous-donations');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/home', [MainController::class, 'index'])->name('home');
    Route::get('/dashboard', [MainController::class, 'dashboard'])->name('dashboard');
    Route::get('/subscribers', [SubscriberController::class, 'index'])->name('subscribers');

    // TODO: this route is for displaying request view -> which will be removed
    // since whenever user comes from github repo to openpledge, repos should be automatically created
    // hwoever this could be used if user searched repos on open pledge and cant find it's own
    // but mybe it would be better when 0 results to request all github repos with certain filters
    // and if found automatically add and show index page...
    Route::get('/repositories/request', [RepositoryController::class, 'getRequestNew'])->name('repositories-request-get');

    Route::resource('repositories', RepositoryController::class)->only('index', 'store');
    Route::resource('issues', IssueController::class)->only(['store'])->except(['show']);
    Route::resource('campaigns', CampaignController::class);
    Route::resource('donations', DonationController::class)->only('index', 'show', 'store');


    Route::get('issues/{issue}/donations', [IssueController::class, 'donations'])->name('issues.donations');

    Route::post('issues/solve', [IssueController::class, 'solve'])->name('issues.solve');

    // override of profile route
    Route::get('/user/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/user/profile/settings', [ProfileController::class, 'settings'])->name('profile.settings');
    Route::post('/user/profile/settings/anonymous-pledging', [ProfileController::class, 'updateAnonymousPledging'])->name('profile.settings.anonymous-pledging');

    // Get favorites from currently authenticated user
    Route::get('/user/favorites', [ProfileController::class, 'getFavorites'])->name('profile.favorites');

    // Store favorites
    Route::post('/favorites', [FavoriteController::class, 'store'])->name('favorites.store');

    // All favorites page for currently authenticated user
    Route::get('/user/profile/favorites', [ProfileController::class, 'showAuthUsersFavorites'])->name('profile.favorites-show');

    // Get active issues from currently authenticated user
    Route::get('/user/actives', [ProfileController::class, 'getAuthUsersActiveIssues'])->name('profile.actives');

    // All active issues page for currently authenticated user
    Route::get('/user/profile/actives', [ProfileController::class, 'showAuthUsersActiveIssues'])->name('profile.actives-show');

    // Get finished issues from currently authenticated user
    Route::get('/user/finished', [ProfileController::class, 'getAuthUsersFinishedIssues'])->name('profile.finished');

    // All finished issues page for currently authenticated user
    Route::get('/user/profile/finished', [ProfileController::class, 'showAuthUsersFinishedIssues'])->name('profile.finished-show');

    Route::post('/user/new-user-quiz-submission', [UserController::class, 'handleNewUserQuizSubmission'])->name('user.new-user-quiz');

    Route::post('/subscribe-user', [SubscriberController::class, 'subscribeUser']);
    
    // Stripe Connect routes
    Route::get('/stripe/connect', [StripeConnectController::class, 'stripeConnect'])->name('stripe.connect');
    Route::get('/stripe/create-account-link', [StripeConnectController::class, 'createAccountLink'])->name('stripe.create.account.link');
    Route::get('/stripe/onboarding/refresh', [StripeConnectController::class, 'onboardingRefresh'])->name('stripe.onboarding.refresh');
    Route::get('/stripe/onboarding/return', [StripeConnectController::class, 'onboardingReturn'])->name('stripe.onboarding.return');
    Route::get('/stripe/verify-installation', [StripeConnectController::class, 'verifyInstallation'])->name('stripe.verify.installation');
    Route::get('/stripe/dashboard/link', [StripeConnectController::class, 'getDashboardLink'])->name('stripe.dashboard.link');

    // GitHub App integration route
    Route::get('/github/installation/callback', [GithubController::class, 'handleGithubAppCallback'])->name('github.installation.callback');

    // Search
    Route::get('/search', [SearchController::class, 'getSearchResults'])->name('search');
});

Route::get('/auth/github/callback', [GithubController::class, 'callback'])->name('callback');
Route::get('/auth/github', [GithubController::class, 'redirect'])->name('redirect');

Route::get('/unsubscribe-user', [SubscriberController::class, 'unsubscribe'])->name('unsubscribe');

// GitHub App Webhook
Route::post('/github/webhook', [GithubController::class, 'handleGithubAppWebhook'])->name('github.webhook');


Route::get('/{any}', function () {
    return Inertia::render('Error');
})->where('any', '.*')->name('error');