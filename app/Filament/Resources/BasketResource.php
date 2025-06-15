<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BasketResource\Pages;
use App\Filament\Resources\BasketResource\RelationManagers;
use App\Listeners\BasketUpdatedWithPaymentType;
use App\Models\Basket;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Log;

class BasketResource extends Resource
{
    protected static ?string $model = Basket::class;
    public static function getNavigationLabel(): string
    {
        return 'Sepet';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Sepet';
    }
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Toggle::make('is_completed')
                    ->label('Alışveriş Tamamlandı')
                    ->reactive()
                    ->afterStateUpdated(fn ($state, callable $set) => $set('is_shopping', !$state)),

                Toggle::make('is_shopping')
                    ->reactive()
                    ->afterStateUpdated(fn ($state, callable $set) => $set('is_completed', !$state)),

                Forms\Components\Select::make('payment_type')
                    ->label('Ödeme Türü')
                    ->required()
                    ->options([
                        'Nakit' => 'Nakit',
                        'Kredi Kart' => 'Kredi Kart',
                        'Eft/Havale' => 'Eft/Havale',
                    ])
            ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->query(
                static::getModel()::query()->where('is_completed', false)
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('user.name')->label('İsim Soyisim')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('total_price')->label('Toplam Tutar')
                    ->getStateUsing(function ($record) {
                        return $record->basketItems->sum(function ($item) {
                            return (optional($item->product)->price ?? 0) * $item->quantity;
                        });
                    })
                    ->money('try'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Ödeme Yap')->icon('heroicon-o-pencil'),
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
            'index' => Pages\ListBaskets::route('/'),
        ];
    }
}
