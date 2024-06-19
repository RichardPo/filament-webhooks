<?php

namespace RichardPost\FilamentWebhooks;

use Closure;

class TriggerGroup
{
    protected ?string $urlPrefix = null;

    protected array $triggers = [];

    protected ?Closure $beforeHandleNotification = null;

    protected ?Closure $subscribeUsing = null;

    protected ?Closure $unsubscribeUsing = null;
    protected ?Closure $handleLifecycleNotificationUsing = null;

    protected ?Closure $getSuccessfulResponseUsing = null;

    public function __construct(
        protected string $name
    ) {}

    public static function make(string $name): static
    {
        return new static($name);
    }

    public function triggers(array $triggers): static
    {
        $this->triggers = array_merge($this->triggers, $triggers);

        return $this;
    }

    public function getTriggers(): array
    {
        return collect($this->triggers)
            ->map(function (Trigger $trigger) {
                if(! $trigger->subscribeUsing) {
                    $trigger->subscribeUsing($this->subscribeUsing ?? fn () => false);
                }

                if(! $trigger->unsubscribeUsing) {
                    $trigger->unsubscribeUsing($this->unsubscribeUsing ?? fn () => false);
                }

                if(! $trigger->beforeHandleNotificationUsing) {
                    $trigger->beforeHandleNotificationUsing($this->beforeHandleNotification ?? fn () => true);
                }

                if(! $trigger->handleLifecycleNotificationUsing) {
                    $trigger->handleLifecycleNotificationUsing($this->handleLifecycleNotificationUsing ?? fn () => abort(500));
                }

                if(! $trigger->getSuccessfulResponseUsing) {
                    $trigger->getSuccesfulResponseUsing($this->getSuccessfulResponseUsing ?? fn () => response());
                }

                return $trigger;
            })
            ->toArray();
    }

    public function beforeHandleNotificationUsing(Closure $callback): static
    {
        $this->beforeHandleNotification = $callback;

        return $this;
    }

    public function subscribeUsing(Closure $callback): static
    {
        $this->subscribeUsing = $callback;

        return $this;
    }

    public function unsubscribeUsing(Closure $callback): static
    {
        $this->unsubscribeUsing = $callback;

        return $this;
    }

    public function handleLifecycleNotificationUsing(Closure $callback): static
    {
        $this->handleLifecycleNotificationUsing = $callback;

        return $this;
    }

    public function getSuccesfulResponseUsing(Closure $callback): static
    {
        $this->getSuccessfulResponseUsing = $callback;

        return $this;
    }
}
