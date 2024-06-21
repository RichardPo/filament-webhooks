<?php

namespace RichardPost\FilamentWebhooks\Filament\Resources\WebhookResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
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

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if(! $record->update($data)) {
            Notification::make()
                ->title('Could not update webhook')
                ->danger()
                ->send();

            $this->halt();
        }

        return $record;
    }
}
