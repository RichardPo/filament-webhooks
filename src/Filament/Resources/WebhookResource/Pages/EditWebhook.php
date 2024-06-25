<?php

namespace RichardPost\FilamentWebhooks\Filament\Resources\WebhookResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use RichardPost\FilamentWebhooks\Filament\Resources\WebhookResource;
use RichardPost\FilamentWebhooks\Models\Webhook;

class EditWebhook extends EditRecord
{
    protected static string $resource = WebhookResource::class;

    public array $cache = [];

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->action(function (Webhook $webhook, Actions\DeleteAction $action) {
                    if(! $webhook->delete()) {
                        Notification::make()
                            ->title('Could not delete webhook')
                            ->danger()
                            ->send();

                        return;
                    }

                    $action->success();
                }),
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
