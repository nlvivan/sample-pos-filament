<?php

namespace App\Filament\Resources;

use App\Exports\TransactionExport;
use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers\OrdersRelationManager;
use App\Filament\Resources\TransactionResource\Widgets\StatsOverview;
use App\Models\Transaction;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $navigationGroup = 'Shop';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make([
                    TextInput::make('transaction_number')
                        ->disabled(),
                    TextInput::make('transaction_amount')
                        ->label('Amount Paid')
                        ->disabled()
                        ->prefix('P'),
                    TextInput::make('cash_amount')
                        ->label('Cash')
                        ->disabled()
                        ->prefix('P'),
                    TextInput::make('change')
                        ->label('Change')
                        ->disabled()
                        ->prefix('P'),
                ]),
            ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('transaction_number')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Customer name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('transaction_amount')
                    ->label('Total Amount')
                    ->sortable()
                    ->formatStateUsing(fn (string $state): string => __("P {$state}"))
                    ->searchable(),
                TextColumn::make('cash_amount')
                    ->label('Cash Amount')
                    ->sortable()
                    ->formatStateUsing(fn (string $state): string => __("P {$state}"))
                    ->searchable(),
                TextColumn::make('change')
                    ->label('Change')
                    ->sortable()
                    ->formatStateUsing(fn (string $state): string => __("P {$state}"))
                    ->searchable(),
                TextColumn::make('created_at')->label('Transaction Date')->dateTime(),
            ])
            ->filters([
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from'),
                        DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                BulkAction::make('delete')
                    ->label('Export Selected')
                    ->icon('heroicon-o-document-download')
                    ->action(fn (Collection $records) => (new TransactionExport($records))->download('transaction.xlsx')),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            OrdersRelationManager::class,
        ];
    }

    public static function getWidgets(): array
    {
        return [
            StatsOverview::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
            'view' => Pages\ViewTransaction::route('/{record}'),
        ];
    }
}
