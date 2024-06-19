<?php

namespace RichardPost\FilamentWebhooks;

use Closure;
use Exception;
use Filament\Forms\Components\Section;
use Illuminate\Http\Response;
use RichardPost\FilamentWebhooks\Models\Webhook;

class Trigger
{
    protected ?string $label = null;

    protected array $form = [];

    protected ?Closure $subscribeUsing = null;

    protected ?Closure $unsubscribeUsing = null;

    protected ?Closure $handleLifecycleNotificationUsing = null;

    protected ?Closure $beforeHandleNotification = null;

    protected ?Closure $getExternalResourcesUsing = null;

    public function __construct(
        protected string $name
    ) {}

    public static function make(string $name): static
    {
        return new static($name);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label ?? 'Trigger';
    }

    public function form(array $fields): static
    {
        $this->form = $fields;

        return $this;
    }

    public function getForm(): Section
    {
        return Section::make('Trigger')
            ->statePath('trigger')
            ->schema($this->form);
    }

    public function subscribeUsing(Closure $callback): static
    {
        $this->subscribeUsing = $callback;

        return $this;
    }

    public function getSubscribeUsing(): ?Closure
    {
        return $this->subscribeUsing;
    }

    public function unsubscribeUsing(Closure $callback): static
    {
        $this->unsubscribeUsing = $callback;

        return $this;
    }

    public function getUnsubscribeUsing(): ?Closure
    {
        return $this->unsubscribeUsing;
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

    public function handleLifecycleNotificationUsing(?Closure $callback): static
    {
        $this->handleLifecycleNotificationUsing = $callback;

        return $this;
    }

    public function getHandleLifecycleNotificationUsing(): ?Closure
    {
        return $this->handleLifecycleNotificationUsing;
    }

    public function getExternalResourcesUsing(?Closure $callback): static
    {
        $this->getExternalResourcesUsing = $callback;

        return $this;
    }

    /**
     * @throws Exception
     */
    public function subscribe(Webhook $webhook): array|bool
    {
        $callback = $this->getSubscribeUsing();

        if(! $callback) {
            throw new Exception("Missing subscribeUsing method on trigger '{$this->name}'");
        }

        return $callback($webhook->trigger);
    }

    /**
     * @throws Exception
     */
    public function unsubscribe(Webhook $webhook): bool
    {
        $callback = $this->getUnsubscribeUsing();

        if(! $callback) {
            throw new Exception("Missing unsubscribeUsing method on trigger '{$this->name}'");
        }

        return $callback($webhook->trigger);
    }

    /**
     * @throws Exception
     */
    public function handleLifecycleNotification(Webhook $webhook): Response
    {
        $callback = $this->getUnsubscribeUsing();

        if(! $callback) {
            throw new Exception("Missing handleLifecycleNotificationUsing method on trigger '{$this->name}'");
        }

        return $callback($webhook->external_data);
    }

    /**
     * @throws Exception
     */
    public function getExternalResources(Webhook $webhook): array
    {
        $callback = $this->getExternalResourcesUsing;

        if(! $callback) {
            throw new Exception("Missing handleLifecycleNotificationUsing method on trigger '{$this->name}'");
        }

        return $callback($webhook->external_data, function (array $externalData) use ($webhook) {
            $webhook->updateQuietly([
                'external_data' => $externalData
            ]);
        });
    }
}
