<?php

namespace RichardPost\FilamentWebhooks;

use Filament\Contracts\Plugin;
use Filament\Panel;
use RichardPost\FilamentWebhooks\Filament\Resources\WebhookResource;

class FilamentWebhooksPlugin implements Plugin
{
    protected array $triggers = [];

    public static function make(): static
    {
        return new static();
    }

    public function getId(): string
    {
        return 'richardpost-filament-webhooks';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                WebhookResource::class
            ]);
    }

    public function boot(Panel $panel): void
    {
        // TODO: Implement boot() method.
    }

    public function triggers(array $triggers): static
    {
        $this->triggers = array_merge($this->triggers, $triggers);

        return $this;
    }

    public function getTriggers(): array
    {
        $triggers = [];

        collect($this->triggers)
            ->each(function (TriggerGroup|Trigger $triggerOrGroup) use (&$triggers) {
                if($triggerOrGroup instanceof Trigger) {
                    $triggers[] = $triggerOrGroup;
                }

                $triggers = array_merge($triggers, $triggerOrGroup->getTriggers());
            });

        return $triggers;
    }
}
