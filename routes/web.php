<?php

use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\LanguageController;
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

// Frontend Routes (Public - No subscription required)
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'handleContactFrom'])->name('contact.submit');

// News Routes (Require Subscription)
Route::middleware(['auth', 'require.subscription'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/home', function () {
        return redirect()->route('home');
    });
    Route::get('/news', [HomeController::class, 'news'])->name('news');
    Route::get('/news/{slug}', [HomeController::class, 'ShowNews'])->name('news-details');
    Route::get('/news/{slug}/export', [HomeController::class, 'exportNews'])->name('news.export');
});

// Language Switching (Public)
Route::post('/language', LanguageController::class)->name('language');

// Authenticated Routes (Require Subscription for Comments)
Route::middleware(['auth', 'require.subscription'])->group(function () {
    Route::post('/comment', [HomeController::class, 'handleComment'])->name('news-comment');
    Route::post('/replay', [HomeController::class, 'handleReplay'])->name('news-comment-replay');
    Route::post('/comment/delete', [HomeController::class, 'commentDestory'])->name('news-comment-destroy');
});

// Newsletter Subscription (Public)
Route::post('/subscribe', [HomeController::class, 'SubscribeNewsLetter'])->name('subscribe-newsletter');

// Subscription Routes
Route::get('/subscription/plans', [\App\Http\Controllers\Frontend\SubscriptionController::class, 'plans'])->name('subscription.plans');
Route::middleware('auth')->group(function () {
    Route::get('/subscription/checkout/{packageId}', [\App\Http\Controllers\Frontend\SubscriptionController::class, 'checkout'])->name('subscription.checkout');
    Route::post('/subscription/subscribe/{packageId}', [\App\Http\Controllers\Frontend\SubscriptionController::class, 'subscribe'])->name('subscription.subscribe');
    Route::get('/subscription/my-subscription', [\App\Http\Controllers\Frontend\SubscriptionController::class, 'mySubscription'])->name('subscription.my-subscription');
    Route::post('/subscription/cancel', [\App\Http\Controllers\Frontend\SubscriptionController::class, 'cancel'])->name('subscription.cancel');
    
    // User Profile Routes
    Route::get('/profile', [\App\Http\Controllers\Frontend\UserProfileController::class, 'index'])->name('user.profile');
    Route::post('/profile/password/update', [\App\Http\Controllers\Frontend\UserProfileController::class, 'updatePassword'])->name('user.profile.password.update');
    Route::post('/profile/package/change', [\App\Http\Controllers\Frontend\UserProfileController::class, 'changePackage'])->name('user.profile.package.change');
});

require __DIR__.'/auth.php';
