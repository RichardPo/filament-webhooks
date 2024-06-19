<?php

namespace RichardPost\FilamentWebhooks\Filament\Resources;

use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
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
                            ->schema([])
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
