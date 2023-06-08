<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Models\Product;
use Closure;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $navigationGroup = 'Shop';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                    ->schema([
                        Card::make([
                            TextInput::make('name')
                                ->label('Customer name')
                                ->required(),
                        ])->columnSpan(2),
                        Card::make([
                            Placeholder::make('total_amount')
                                ->label('Total Amount')
                                ->content(function ($get) {
                                    return collect($get('fields'))
                                        ->pluck('total_amount')
                                        ->sum();
                                }),
                            Placeholder::make('cash_amount_label')
                                ->label('Cash Amount')
                                ->content(function ($get) {
                                    return $get('cash_amount') ?? 0;
                                }),
                            Placeholder::make('Change_amount')
                                ->label('Change')
                                ->content(function ($get) {
                                    return $get('change') ?? 0;
                                }),
                        ])->columnSpan(1),
                    ])->columns(3),
                Card::make([
                    Wizard::make([
                        Step::make('Order')
                            ->schema([
                                Card::make([

                                    Repeater::make('fields')
                                        ->label('Order Fruits')
                                        ->schema([
                                            Select::make('product_id')
                                                ->label('Product')
                                                ->options(Product::all()->pluck('name', 'id')->toArray())
                                                ->afterStateUpdated(function (callable $get, Closure $set, $state) {
                                                    $product = Product::find($state);

                                                    $set('total_amount', $product->price * $get('quantiy'));
                                                })
                                                ->reactive()
                                                ->required(),
                                            TextInput::make('quantiy')
                                                ->label('Kilograms')
                                                ->default(1)
                                                ->afterStateUpdated(function (callable $get, Closure $set, $state) {
                                                    $product = Product::find($get('product_id'));

                                                    $set('total_amount', $product->price * $state);
                                                })
                                                ->numeric()
                                                ->reactive()
                                                ->postfix('kg')
                                                ->reactive(),
                                            TextInput::make('total_amount')
                                                ->label('Total Amount')
                                                ->default(0)
                                                ->numeric()
                                                ->disabled()
                                                ->reactive(),
                                        ]),
                                ])
                                    ->reactive(),
                            ]),
                        Step::make('payment')
                            ->schema([
                                Card::make([
                                    TextInput::make('cash_amount')
                                        ->label('Cash Amount')
                                        ->numeric()
                                        ->reactive()
                                        ->required()
                                        ->afterStateUpdated(function (callable $get, Closure $set, $state) {
                                            $totalAmount = collect($get('fields'))
                                                ->pluck('total_amount')
                                                ->sum();
                                            $change = $state - $totalAmount;
                                            $set('change', $change);
                                        }),
                                    TextInput::make('change')
                                        ->label('Change')
                                        ->default(0)
                                        ->numeric()
                                        ->disabled()
                                        ->reactive(),

                                ]),
                            ]),
                    ]),
                ]),

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
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
