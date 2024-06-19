<?php

namespace RichardPost\FilamentWebhooks\Models;

use Illuminate\Database\Eloquent\Model;
use RichardPost\FilamentWebhooks\Models\Traits\HasUuid;
use RichardPost\FilamentWebhooks\Trigger;

class Webhook extends Model
{
    use HasUuid;

    protected $fillable = [
        'name',
        'trigger',
        'actions',
        'external_info'
    ];

    protected $casts = [
        'trigger' => 'array',
        'actions' => 'array',
        'external_info' => 'array'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (self $webhook) {
            $externalInfo = $webhook->getTriggerConfig()->subscribe($webhook);

            if(! $externalInfo) {
                return false;
            }

            $webhook->external_info = $externalInfo;

            return true;
        });

        static::updating(function (self $webhook) {
            $unsubscribed = $webhook->getTriggerConfig()->unsubscribe($webhook);

            if(! $unsubscribed) {
                return false;
            }

            $externalInfo = $webhook->getTriggerConfig()->subscribe($webhook);

            if(! $externalInfo) {
                return false;
            }

            $webhook->external_info = $externalInfo;

            return true;
        });

        static::deleting(function (self $webhook) {
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
