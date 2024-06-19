<?php

namespace RichardPost\FilamentWebhooks;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentWebhooksServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-webhooks')
            ->hasMigrations(['create_webhooks_table']);
    }
}
