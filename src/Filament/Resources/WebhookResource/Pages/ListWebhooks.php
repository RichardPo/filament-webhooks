<?php

namespace RichardPost\FilamentWebhooks\Filament\Resources\WebhookResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use RichardPost\FilamentWebhooks\Filament\Resources\WebhookResource;

class ListWebhooks extends ListRecords
{
    protected static string $resource = WebhookResource::class;

    public array $cache = [];

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
