<?php

namespace RichardPost\FilamentWebhooks\Filament\Resources\WebhookResource\Pages;

use App\Http\Middleware\PreventRequestsDuringMaintenance;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use RichardPost\FilamentWebhooks\Filament\Resources\WebhookResource;
use RichardPost\FilamentWebhooks\Models\Webhook;

class CreateWebhook extends CreateRecord
{
    protected static string $resource = WebhookResource::class;

    public array $cache = [];

    protected function handleRecordCreation(array $data): Model
    {
        $record = new ($this->getModel())($data);

        if (
            static::getResource()::isScopedToTenant() &&
            ($tenant = Filament::getTenant())
        ) {
            return $this->associateRecordWithTenant($record, $tenant);
        }

        if(! $record->save()) {
            Notification::make()
                ->title('Could not create webhook')
                ->danger()
                ->send();

            $this->halt();
        }

        return $record;
    }
}
