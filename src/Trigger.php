<?php

namespace RichardPost\FilamentWebhooks;

use Closure;
use Filament\Forms\Components\Section;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use RichardPost\FilamentWebhooks\Models\Webhook;

class Trigger
{
    protected ?string $label = null;

    protected array $form = [];

    public ?Closure $subscribeUsing = null;

    public ?Closure $unsubscribeUsing = null;

    public ?Closure $handleLifecycleNotificationUsing = null;

    public ?Closure $beforeHandleNotificationUsing = null;

    public ?Closure $getExternalResourcesUsing = null;

    public ?Closure $getSuccessfulResponseUsing = null;

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

    public function unsubscribeUsing(Closure $callback): static
    {
        $this->unsubscribeUsing = $callback;

        return $this;
    }

    public function beforeHandleNotificationUsing(Closure $callback): static
    {
        $this->beforeHandleNotificationUsing = $callback;

        return $this;
    }

    public function handleLifecycleNotificationUsing(?Closure $callback): static
    {
        $this->handleLifecycleNotificationUsing = $callback;

        return $this;
    }

    public function getExternalResourcesUsing(?Closure $callback): static
    {
        $this->getExternalResourcesUsing = $callback;

        return $this;
    }

    public function getSuccesfulResponseUsing(?Closure $callback): static
    {
        $this->getSuccessfulResponseUsing = $callback;

        return $this;
    }

    public function subscribe(Webhook $webhook): array|bool
    {
        $callback = $this->subscribeUsing ?? fn () => false;

        return $callback(
            $webhook->trigger,
            route('filament-webhooks.notify', ['webhook' => $webhook]),
            route('filament-webhooks.lifecycle', ['webhook' => $webhook])
        );
    }

    public function unsubscribe(Webhook $webhook): bool
    {
        $callback = $this->unsubscribeUsing ?? fn () => false;

        return $callback($webhook->external_data);
    }

    public function handleLifecycleNotification(Request $request, Webhook $webhook): Application|Response|ResponseFactory|\Illuminate\Contracts\Foundation\Application
    {
        $callback = $this->handleLifecycleNotificationUsing ?? fn () => abort(500);

        return $callback($request, $webhook->external_data, function () use ($webhook) {
            $webhook->update([
                'external_data' => ['update' => Str::uuid()]
            ]);
        });
    }

    public function getExternalResources(Request $request, Webhook $webhook): array
    {
        $callback = $this->getExternalResourcesUsing ?? fn () => [];

        return $callback($request, $webhook->external_data, function (array $externalData) use ($webhook) {
            $webhook->updateQuietly([
                'external_data' => $externalData
            ]);
        });
    }

    public function getSuccessfulResponse(): Application|Response|ResponseFactory|\Illuminate\Contracts\Foundation\Application
    {
        $callback = $this->getSuccessfulResponseUsing ?? fn () => response();

        return $callback();
    }

    public function beforeHandleNotification(Request $request, Webhook $webhook): bool|Application|Response|ResponseFactory|\Illuminate\Contracts\Foundation\Application
    {
        $callback = $this->beforeHandleNotificationUsing ?? fn () => true;

        return $callback($request, $webhook->trigger);
    }
}
