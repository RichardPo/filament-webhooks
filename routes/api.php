<?php

Route::middleware('api')->prefix('api')->group(function () {
    Route::prefix('webhooks')->as('filament-webhooks.')->group(function () {
        Route::post('{webhook}/notify', );

        Route::post('{webhook}/lifecycle');
    });
});
