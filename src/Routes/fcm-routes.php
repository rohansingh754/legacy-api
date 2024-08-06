<?php

use Illuminate\Support\Facades\Route;
use Webkul\API\Http\Controllers\Admin\NotificationController;

/**
 * FCM Notification routes.
 */
Route::group(['middleware' => ['web', 'admin', 'locale'], 'prefix' => config('app.admin_url')], function () {
    Route::controller(NotificationController::class)->prefix('api_notification')->group(function () {
        Route::get('/', 'index')->name('api.notification.index');

        Route::get('/create', 'create')->name('api.notification.create');

        Route::post('/store', 'store')->name('api.notification.store');

        Route::get('/edit/{id}', 'edit')->name('api.notification.edit');

        Route::put('/edit/{id}', 'update')->name('api.notification.update');

        Route::post('/delete/{id}', 'delete')->name('api.notification.delete');

        Route::post('/massdelete', 'massDestroy')->name('api.notification.mass-delete');

        Route::post('/massupdate', 'massUpdate')->name('api.notification.mass-update');

        Route::get('/send/{id}', 'sendNotification')->name('api.notification.send-notification');

        Route::post('/exist', 'exist')->name('api.notification.cat-product-id');
    });
});
