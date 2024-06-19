<?php

namespace RichardPost\FilamentWebhooks\Filament\Resources\WebhookResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use RichardPost\FilamentWebhooks\Filament\Resources\WebhookResource;

class EditWebhook extends EditRecord
{
    protected static string $resource = WebhookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
