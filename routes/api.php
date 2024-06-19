<?php

use RichardPost\FilamentWebhooks\Http\Controllers\WebhooksController;

Route::middleware('api')->prefix('api')->group(function () {
    Route::prefix('webhooks')->as('filament-webhooks.')->group(function () {
        Route::post('{webhook}/notify', [WebhooksController::class, 'handleNotification'])->name('notify');

        Route::post('{webhook}/lifecycle', [WebhooksController::class, 'handleLifecycleNotification'])->name('lifecycle');
    });
});
