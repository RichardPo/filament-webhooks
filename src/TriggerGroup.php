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
                if(! $trigger->getSubscribeUsing()) {
                    return $trigger->subscribeUsing($this->getSubscribeUsing());
                }

                if(! $trigger->getUnsubscribeUsing()) {
                    return $trigger->unsubscribeUsing($this->getUnsubscribeUsing());
                }

                if(! $trigger->getBeforeHandleNotification()) {
                    return $trigger->beforeHandleNotification($this->getBeforeHandleNotification() ?? fn () => true);
                }

                if(! $trigger->getHandleLifecycleNotificationUsing()) {
                    return $trigger->handleLifecycleNotificationUsing($this->getHandleLifecycleNotificationUsing());
                }

                return $trigger;
            })
            ->toArray();
    }

    public function beforeHandleNotification(Closure $callback): static
    {
        $this->beforeHandleNotification = $callback;

        return $this;
    }

    public function getBeforeHandleNotification(): ?Closure
    {
        return $this->beforeHandleNotification;
    }

    public function subscribeUsing(Closure $callback): static
    {
        $this->subscribeUsing = $callback;

        return $this;
    }

    public function getSubscribeUsing(): Closure
    {
        return $this->subscribeUsing ?? fn () => false;
    }

    public function unsubscribeUsing(Closure $callback): static
    {
        $this->unsubscribeUsing = $callback;

        return $this;
    }

    public function getUnsubscribeUsing(): Closure
    {
        return $this->unsubscribeUsing ?? fn () => false;
    }

    public function handleLifecycleNotificationUsing(Closure $callback): static
    {
        $this->handleLifecycleNotificationUsing = $callback;

        return $this;
    }

    public function getHandleLifecycleNotificationUsing(): ?Closure
    {
        return $this->handleLifecycleNotificationUsing;
    }
}
