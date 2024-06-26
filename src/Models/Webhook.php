<?php

namespace RichardPost\FilamentWebhooks\Models;

use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Model;
use RichardPost\FilamentWebhooks\Enums\WebhookStatus;
use RichardPost\FilamentWebhooks\Models\Traits\HasUuid;
use RichardPost\FilamentWebhooks\Trigger;
use Illuminate\Support\Str;

class Webhook extends Model
{
    use HasUuid;

    protected $fillable = [
        'name',
        'status',
        'trigger',
        'actions',
        'external_data'
    ];

    protected $casts = [
        'trigger' => 'array',
        'actions' => 'array',
        'external_data' => 'array',
        'status' => WebhookStatus::class
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (self $webhook) {
            $id = Str::uuid();

            $temp = $webhook->replicate();

            $temp->id = $id;
            $temp->status = WebhookStatus::Unsubscribed;
            $temp->external_data = [];
            $temp->saveQuietly();

            $externalData = $temp->getTriggerConfig()->subscribe($temp);

            if (!is_array($externalData)) {
                $temp->deleteQuietly();

                return false;
            }

            $temp->deleteQuietly();

            $webhook->id = $id;
            $webhook->status = WebhookStatus::Subscribed;
            $webhook->external_data = $externalData;

            return true;
        });

        static::updating(function (self $webhook) {
            if($webhook->status === WebhookStatus::Subscribed) {
                $unsubscribed = $webhook->getTriggerConfig()->unsubscribe($webhook);

                if(! $unsubscribed) {
                    return false;
                }
            }

            $externalData = $webhook->getTriggerConfig()->subscribe($webhook);

            if (! is_array($externalData)) {
                $webhook->updateQuietly([
                    'status' => WebhookStatus::Unsubscribed,
                    'external_data' => []
                ]);

                return false;
            }

            $webhook->status = WebhookStatus::Subscribed;
            $webhook->external_data = $externalData;

            return true;
        });

        static::deleting(function (self $webhook) {
            if($webhook->status !== WebhookStatus::Subscribed) {
                return true;
            }

            return $webhook->getTriggerConfig()->unsubscribe($webhook);
        });
    }

    public function getTriggerConfig(): Trigger
    {
        $triggerName = $this->trigger['name'];

        return collect(filament('richardpost-filament-webhooks')->getTriggers())
            ->filter(fn (Trigger $trigger) => $trigger->getName() === $triggerName)
            ->firstOrFail();
    }
}
