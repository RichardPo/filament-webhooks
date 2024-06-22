<?php

namespace RichardPost\FilamentWebhooks;

use Closure;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\TextInput;

class Action
{
    protected ?string $label = null;

    protected array $form = [];

    protected ?Closure $action = null;

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
        return $this->label ?? $this->name;
    }

    public function form(array $fields): static
    {
        $this->form = $fields;

        return $this;
    }

    public function getForm(): Block
    {
        return Block::make($this->getName())
            ->label($this->getLabel())
            ->schema($this->form);
    }

    public function action(Closure $callback): static
    {
        $this->action = $callback;

        return $this;
    }

    public function executeAction(array $action, array $resource): void
    {
        $callback = $this->action;

        if(! $callback) {
            return;
        }

        $callback($action, $resource);
    }
}
