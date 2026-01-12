<?php

use App\Http\Controllers\Admin\AboutController;
use App\Http\Controllers\Admin\AdController;
use App\Http\Controllers\Admin\AdminAuthenticationController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AuthorController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FooterGridOneController;
use App\Http\Controllers\Admin\FooterGridThreeController;
use App\Http\Controllers\Admin\FooterGridTwoController;
use App\Http\Controllers\Admin\FooterInfoController;
use App\Http\Controllers\Admin\HomeSectionSettingController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\LocalizationController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RolePermisionController;
use App\Http\Controllers\Admin\RoleUserController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SocialCountController;
use App\Http\Controllers\Admin\SocialLinkController;
use App\Http\Controllers\Admin\SubscriptionPackageController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\ImageGalleryController;
use App\Http\Controllers\Admin\VideoGalleryController;
use App\Http\Controllers\Admin\SupportTicketController;
use App\Models\FooterGridOne;
use App\Models\Setting;
use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'admin', 'as' => 'admin.'], function(){

    Route::get('login', [AdminAuthenticationController::class, 'login'])->name('login');
    Route::post('login', [AdminAuthenticationController::class, 'handleLogin'])->name('handle-login');
    Route::post('logout', [AdminAuthenticationController::class, 'logout'])->name('logout');

    /** Reset passeord */
    Route::get('forgot-password', [AdminAuthenticationController::class, 'forgotPassword'])->name('forgot-password');
    Route::post('forgot-password', [AdminAuthenticationController::class, 'sendResetLink'])->name('forgot-password.send');

    Route::get('reset-password/{token}', [AdminAuthenticationController::class, 'resetPassword'])->name('reset-password');
    Route::post('reset-password', [AdminAuthenticationController::class, 'handleResetPassword'])->name('reset-password.send');


});

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['admin']], function(){
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    /**Profile Routes */
    Route::put('profile-password-update/{id}', [ ProfileController::class, 'passwordUpdate'])->name('profile-password.update');
    Route::resource('profile', ProfileController::class);

    /** Language Route */
    Route::resource('language', LanguageController::class);

    /** Category Route */
    Route::get('category/create/{lang?}', [CategoryController::class, 'create'])->name('category.create');
    Route::post('category', [CategoryController::class, 'store'])->name('category.store');
    Route::get('category/{id}/edit', [CategoryController::class, 'edit'])->name('category.edit');
    Route::put('category/{id}', [CategoryController::class, 'update'])->name('category.update');
    Route::delete('category/{id}', [CategoryController::class, 'destroy'])->name('category.destroy');
    Route::get('category', [CategoryController::class, 'index'])->name('category.index');

    /** Author Route */
    Route::get('author/create/{lang?}', [AuthorController::class, 'create'])->name('author.create');
    Route::post('author', [AuthorController::class, 'store'])->name('author.store');
    Route::get('author/{id}/edit', [AuthorController::class, 'edit'])->name('author.edit');
    Route::put('author/{id}', [AuthorController::class, 'update'])->name('author.update');
    Route::delete('author/{id}', [AuthorController::class, 'destroy'])->name('author.destroy');
    Route::get('author', [AuthorController::class, 'index'])->name('author.index');

    /** News Route */
    Route::get('fetch-news-category', [NewsController::class, 'fetchCategory'])->name('fetch-news-category');
    Route::get('fetch-news-subcategories', [NewsController::class, 'fetchSubcategories'])->name('fetch-news-subcategories');
    Route::get('toggle-news-status', [NewsController::class, 'toggleNewsStatus'])->name('toggle-news-status');
    Route::post('update-news-order-position', [NewsController::class, 'updateOrderPosition'])->name('update-news-order-position');
    Route::get('news-copy/{id}', [NewsController::class, 'copyNews'])->name('news-copy');
    Route::get('pending-news', [NewsController::class, 'pendingNews'])->name('pending.news');
    Route::put('approve-news', [NewsController::class, 'approveNews'])->name('approve.news');

    /** News Sorting Routes */
    Route::prefix('news-sorting')->name('news-sorting.')->group(function() {
        Route::get('/', [NewsController::class, 'newsSorting'])->name('index');
        Route::get('/get-news/{type}', [NewsController::class, 'getNewsByType'])->name('get-news');
        Route::post('/update-order', [NewsController::class, 'updateSortingOrder'])->name('update-order');
        Route::post('/add-to-tab', [NewsController::class, 'addNewsToTab'])->name('add-to-tab');
        Route::post('/remove-from-tab', [NewsController::class, 'removeNewsFromTab'])->name('remove-from-tab');
    });

    // News routes with language-specific create
    Route::get('news/create/{lang?}', [NewsController::class, 'create'])->name('news.create');
    Route::post('news', [NewsController::class, 'store'])->name('news.store');
    Route::get('news/{id}/edit', [NewsController::class, 'edit'])->name('news.edit');
    Route::put('news/{id}', [NewsController::class, 'update'])->name('news.update');
    Route::delete('news/{id}', [NewsController::class, 'destroy'])->name('news.destroy');
    Route::get('news', [NewsController::class, 'index'])->name('news.index');
    Route::get('news/{id}', [NewsController::class, 'show'])->name('news.show');

    /** Home Section Setting Route */
    Route::get('home-section-setting', [HomeSectionSettingController::class, 'index'])->name('home-section-setting.index');
    Route::put('home-section-setting', [HomeSectionSettingController::class, 'update'])->name('home-section-setting.update');

    /** Social Count Route */
    Route::resource('social-count', SocialCountController::class);

    /** Ad Route */
    Route::resource('ad', AdController::class);

    /** Image Upload Route (for Summernote) - Legacy support */
    Route::post('upload-image', [\App\Http\Controllers\Admin\ImageUploadController::class, 'uploadImage'])->name('upload-image');

    /** Media Library Routes */
    Route::prefix('media-library')->name('media-library.')->group(function() {
        Route::get('/', [\App\Http\Controllers\Admin\MediaController::class, 'index'])->name('index');
        Route::get('/api', [\App\Http\Controllers\Admin\MediaController::class, 'getMediaForEditor'])->name('api');
        Route::post('/', [\App\Http\Controllers\Admin\MediaController::class, 'store'])->name('store');
        Route::get('/{id}', [\App\Http\Controllers\Admin\MediaController::class, 'show'])->name('show');
        Route::put('/{id}', [\App\Http\Controllers\Admin\MediaController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\MediaController::class, 'destroy'])->name('destroy');
    });

    /** Gallery Routes */
    Route::resource('image-gallery', ImageGalleryController::class);
    Route::resource('video-gallery', VideoGalleryController::class);

    /** Subscription Package Route */
    Route::resource('subscription-package', SubscriptionPackageController::class);

    /** User Subscription Route */
    Route::get('user-subscription', [\App\Http\Controllers\Admin\UserSubscriptionController::class, 'index'])->name('user-subscription.index');
    Route::get('user-subscription/{id}/edit', [\App\Http\Controllers\Admin\UserSubscriptionController::class, 'edit'])->name('user-subscription.edit');
    Route::put('user-subscription/{id}', [\App\Http\Controllers\Admin\UserSubscriptionController::class, 'update'])->name('user-subscription.update');
    Route::put('user-subscription/{id}/expiry-date', [\App\Http\Controllers\Admin\UserSubscriptionController::class, 'updateExpiryDate'])->name('user-subscription.update-expiry-date');
    Route::get('user-subscription/{id}/approve', [\App\Http\Controllers\Admin\UserSubscriptionController::class, 'approve'])->name('user-subscription.approve');
    Route::delete('user-subscription/{id}', [\App\Http\Controllers\Admin\UserSubscriptionController::class, 'destroy'])->name('user-subscription.destroy');

    /** Social links Route */
    Route::resource('social-link', SocialLinkController::class);

    /** Footer Info Route */
    Route::resource('footer-info', FooterInfoController::class);

    /** Footer Grid One Route */
    Route::post('footer-grid-one-title', [FooterGridOneController::class, 'handleTitle'])->name('footer-grid-one-title');
    Route::resource('footer-grid-one', FooterGridOneController::class);

    /** Footer Grid Two Route */
    Route::post('footer-grid-two-title', [FooterGridTwoController::class, 'handleTitle'])->name('footer-grid-two-title');
    Route::resource('footer-grid-two', FooterGridTwoController::class);

    /** Footer Grid Two Route */
    Route::post('footer-grid-three-title', [FooterGridThreeController::class, 'handleTitle'])->name('footer-grid-three-title');
    Route::resource('footer-grid-three', FooterGridThreeController::class);

    /** About page Route */
    Route::get('about', [AboutController::class, 'index'])->name('about.index');
    Route::put('about', [AboutController::class, 'update'])->name('about.update');

    /** Contact page Route */
    Route::get('contact', [ContactController::class, 'index'])->name('contact.index');
    Route::put('contact', [ContactController::class, 'update'])->name('contact.update');

    /** Contact Message Route */
    Route::get('contact-message', [ContactMessageController::class, 'index'])->name('contact-message.index');
    Route::post('contact-send-replay', [ContactMessageController::class, 'sendReplay'])->name('contact.send-replay');

    /** Settings Routes */
    Route::get('setting', [SettingController::class, 'index'])->name('setting.index');
    /** Settings Routes */
    Route::put('general-setting', [SettingController::class, 'updateGeneralSetting'])->name('general-setting.update');
    Route::put('seo-setting', [SettingController::class, 'updateSeoSetting'])->name('seo-setting.update');
    Route::put('appearance-setting', [SettingController::class, 'updateAppearanceSetting'])->name('appearance-setting.update');
    Route::put('watermark-setting', [SettingController::class, 'updateWatermarkSetting'])->name('watermark-setting.update');
    Route::put('email-setting', [SettingController::class, 'updateEmailSetting'])->name('email-setting.update');
    Route::post('email-setting/test', [SettingController::class, 'testEmailSetting'])->name('email-setting.test');

    /** Role and Permissions Routes */
    Route::get('role', [RolePermisionController::class, 'index'])->name('role.index');
    Route::get('role/create', [RolePermisionController::class, 'create'])->name('role.create');
    Route::post('role/create', [RolePermisionController::class, 'store'])->name('role.store');
    Route::get('role/{id}/edit', [RolePermisionController::class, 'edit'])->name('role.edit');
    Route::put('role/{id}/edit', [RolePermisionController::class, 'update'])->name('role.update');
    Route::delete('role/{id}/destory', [RolePermisionController::class, 'destory'])->name('role.destory');

    /** Admin User Routes */
    Route::resource('role-users', RoleUserController::class);

    /** Localization Routes */
    Route::get('admin-localization', [LocalizationController::class, 'adminIndex'])->name('admin-localization.index');
    Route::get('frontend-localization', [LocalizationController::class, 'frontnedIndex'])->name('frontend-localization.index');

    Route::post('extract-localize-string', [LocalizationController::class, 'extractLocalizationStrings'])->name('extract-localize-string');

    Route::post('update-lang-string', [LocalizationController::class, 'updateLangString'])->name('update-lang-string');


    Route::post('translate-string', [LocalizationController::class, 'translateString'])->name('translate-string');

        /** Analytics Routes */
        Route::prefix('analytics')->name('analytics.')->group(function() {
            Route::get('/', [AnalyticsController::class, 'index'])->name('index');
            Route::get('/real-time', [AnalyticsController::class, 'realTime'])->name('real-time');
            Route::get('/date-wise', [AnalyticsController::class, 'dateWise'])->name('date-wise');
            Route::get('/country-wise', [AnalyticsController::class, 'countryWise'])->name('country-wise');
            Route::get('/organic', [AnalyticsController::class, 'organic'])->name('organic');
            Route::get('/repeaters', [AnalyticsController::class, 'repeaters'])->name('repeaters');
            Route::get('/export', [AnalyticsController::class, 'export'])->name('export');
            Route::get('/settings', [AnalyticsController::class, 'settings'])->name('settings');
            Route::put('/settings', [AnalyticsController::class, 'updateSettings'])->name('settings.update');
            Route::get('/most-visited-ips', [AnalyticsController::class, 'mostVisitedIps'])->name('most-visited-ips');
            Route::get('/most-viewed-pages', [AnalyticsController::class, 'mostViewedPages'])->name('most-viewed-pages');
            Route::get('/bot-activity', [AnalyticsController::class, 'botActivity'])->name('bot-activity');
            Route::post('/block-ip', [AnalyticsController::class, 'blockIp'])->name('block-ip');
            Route::post('/unblock-ip', [AnalyticsController::class, 'unblockIp'])->name('unblock-ip');
        });

    /** Activity Log Routes */
    Route::prefix('activity-log')->name('activity-log.')->group(function() {
        Route::get('/', [ActivityLogController::class, 'index'])->name('index');
        Route::get('/deleted', [ActivityLogController::class, 'deleted'])->name('deleted');
        Route::get('/export', [ActivityLogController::class, 'export'])->name('export');
        Route::get('/settings', [ActivityLogController::class, 'settings'])->name('settings');
        Route::put('/settings', [ActivityLogController::class, 'updateSettings'])->name('settings.update');
        Route::get('/model/{modelType}/{modelId}', [ActivityLogController::class, 'modelActivity'])->name('model-activity');
        Route::get('/user/{userId}/{userType}', [ActivityLogController::class, 'userActivity'])->name('user-activity');
        Route::post('/{id}/restore', [ActivityLogController::class, 'restore'])->name('restore');
        Route::get('/{id}', [ActivityLogController::class, 'show'])->name('show');
    });

    /** Support Ticket Routes */
    Route::prefix('support-tickets')->name('support-tickets.')->group(function() {
        Route::post('/{id}/assign', [SupportTicketController::class, 'assign'])->name('assign');
        Route::post('/{id}/reply', [SupportTicketController::class, 'addReply'])->name('reply');
    });
    Route::resource('support-tickets', SupportTicketController::class);
    
    /** Support Ticket Tags Routes */
    Route::resource('support-ticket-tags', \App\Http\Controllers\Admin\SupportTicketTagController::class);

    /** Watermark Settings Routes */
    Route::get('watermark-settings', [\App\Http\Controllers\Admin\WatermarkSettingController::class, 'index'])->name('watermark-settings.index');
    Route::put('watermark-settings', [\App\Http\Controllers\Admin\WatermarkSettingController::class, 'update'])->name('watermark-settings.update');

    /** Subscriber Routes */
    Route::prefix('subscriber')->name('subscriber.')->group(function() {
        Route::get('/', [\App\Http\Controllers\Admin\SubscriberController::class, 'index'])->name('index');
        Route::get('/export', [\App\Http\Controllers\Admin\SubscriberController::class, 'export'])->name('export');
        Route::post('/{id}/toggle-email', [\App\Http\Controllers\Admin\SubscriberController::class, 'toggleEmailNotifications'])->name('toggle-email');
        Route::post('/{id}/toggle-full-news', [\App\Http\Controllers\Admin\SubscriberController::class, 'toggleFullNewsEmail'])->name('toggle-full-news');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\SubscriberController::class, 'destroy'])->name('destroy');
    });

    /** Subscriber Notification Settings Routes */
    Route::get('subscriber-notification-settings', [\App\Http\Controllers\Admin\SubscriberNotificationSettingsController::class, 'index'])->name('subscriber-notification-settings.index');
    Route::put('subscriber-notification-settings', [\App\Http\Controllers\Admin\SubscriberNotificationSettingsController::class, 'update'])->name('subscriber-notification-settings.update');

    /** Email Reports Routes */
    Route::prefix('email-report')->name('email-report.')->group(function() {
        Route::get('/', [\App\Http\Controllers\Admin\EmailReportController::class, 'index'])->name('index');
        Route::get('/pending', [\App\Http\Controllers\Admin\EmailReportController::class, 'pending'])->name('pending');
    });

});


