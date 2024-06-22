<?php

namespace RichardPost\FilamentWebhooks\Filament\Resources;

use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use RichardPost\FilamentWebhooks\Action;
use RichardPost\FilamentWebhooks\Enums\WebhookStatus;
use RichardPost\FilamentWebhooks\Filament\Resources\WebhookResource\Pages;
use RichardPost\FilamentWebhooks\Filament\Resources\WebhookResource\RelationManagers;
use RichardPost\FilamentWebhooks\Models\Webhook;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use RichardPost\FilamentWebhooks\Trigger;

class WebhookResource extends Resource
{
    protected static ?string $model = Webhook::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('General')
                    ->schema([
                        TextInput::make('name')
                            ->label('Name')
                            ->required(),

                        Select::make('trigger.name')
                            ->label('Trigger')
                            ->required()
                            ->live()
                            ->searchable()
                            ->options(
                                fn () => collect(filament('richardpost-filament-webhooks')->getTriggers())
                                    ->mapWithKeys(fn (Trigger $trigger) => [$trigger->getName() => $trigger->getLabel()])
                                    ->toArray()
                            )
                    ]),

                ...collect(filament('richardpost-filament-webhooks')->getTriggers())
                    ->map(
                        fn (Trigger $trigger) => $trigger
                            ->getForm()
                            ->visible(
                                fn (Get $get) => $get('trigger.name') === $trigger->getName()
                            )
                    )
                    ->toArray(),

                Section::make('Actions')
                    ->schema([
                        Builder::make('actions')
                            ->blockNumbers(false)
                            ->blocks(
                                collect(filament('richardpost-filament-webhooks')->getActions())
                                    ->map(fn (Action $action) => $action->getForm())
                                    ->toArray()
                            )
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->limit(40)
                    ->extraHeaderAttributes([
                        'style' => 'min-width: 200px;'
                    ]),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn (Webhook $record) => WebhookStatus::getLabel($record->status))
                    ->color(fn (Webhook $record) => WebhookStatus::getColor($record->status))
                    ->tooltip(fn (Webhook $record) => match($record->status) {
                        WebhookStatus::Unsubscribed => 'This webhook was created, but there was an error during the subscription process. Consider updating the webhook or recreating it.',
                        default => null
                    })
                    ->badge()
                    ->extraHeaderAttributes([
                        'style' => 'min-width: 0px; width: 0px;'
                    ])
                    ->extraCellAttributes([
                        'style' => 'min-width: 0px;'
                    ]),

                Tables\Columns\TextColumn::make('trigger.name')
                    ->label('Trigger')
                    ->formatStateUsing(function (string $state) {
                        $trigger = collect(filament('richardpost-filament-webhooks')->getTriggers())
                            ->filter(fn (Trigger $trigger) => $trigger->getName() === $state)
                            ->first();

                        if(! $trigger) {
                            return '';
                        }

                        return $trigger->getLabel();
                    })
                    ->limit(40)
                    ->badge()
                    ->tooltip(function (string $state) {
                        $trigger = collect(filament('richardpost-filament-webhooks')->getTriggers())
                            ->filter(fn (Trigger $trigger) => $trigger->getName() === $state)
                            ->first();

                        if(! $trigger) {
                            return null;
                        }

                        return $trigger->getLabel();
                    })
                    ->extraHeaderAttributes([
                        'style' => 'min-width: 0px; width: 0px;'
                    ])
                    ->extraCellAttributes([
                        'style' => 'min-width: 0px;'
                    ]),

                Tables\Columns\TextColumn::make('space')
                    ->label('')
                    ->grow()
            ])
            ->filters([
                //
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(function (Collection $records) {
                            $records->each(function (Webhook $record) {
                                if(! $record->delete()) {
                                    Notification::make()
                                        ->title("Webhook '{$record->name}' could not be deleted")
                                        ->danger()
                                        ->send();

                                    return;
                                }

                                Notification::make()
                                    ->title("Webhook '{$record->name}' deleted")
                                    ->success()
                                    ->send();
                            });
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWebhooks::route('/'),
            'create' => Pages\CreateWebhook::route('/create'),
            'edit' => Pages\EditWebhook::route('/{record}/edit'),
        ];
    }
}
