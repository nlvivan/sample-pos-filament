<?php

namespace App\Filament\Resources\TransactionResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    protected static ?string $recordTitleAttribute = 'quantity';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('transaction.transaction_number')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('Product.name')
                    ->label('Product')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('quantity')
                    ->sortable()
                    ->formatStateUsing(fn (string $state): string => __("{$state} kg"))
                    ->searchable(),
                TextColumn::make('total_amount')
                    ->sortable()
                    ->formatStateUsing(fn (string $state): string => __("P {$state}"))
                    ->searchable(),
                TextColumn::make('transaction.name')
                    ->label('Customer Name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
