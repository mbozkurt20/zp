<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockResource\Pages;
use App\Filament\Resources\StockResource\RelationManagers;
use App\Models\Product;
use App\Models\Stock;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class StockResource extends Resource
{
    protected static ?string $model = Product::class;
    public static function getNavigationLabel(): string
    {
        return 'Stoklar';
    }
    public static function getPluralModelLabel(): string
    {
        return 'Stoklar';
    }
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->label('Ürün Adı')->required(),
                TextInput::make('price')
                    ->numeric()
                    ->label('Fiyat')
                    ->required(),
                TextInput::make('purchase_price')
                    ->numeric()
                    ->label('Alış Fiyatı')
                    ->required(),
                Forms\Components\Select::make('stock_type')->label('Stok Türü')->options([
                    'Kilogram' => 'Kilogram',
                    'Gram' => 'Gram',
                    'Litre' => 'Litre',
                    'Adet' => 'Adet',
                ])->required(),

                TextInput::make('quantity')->minValue(1)->label('Stok Miktar')->numeric()->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('barcode')->label('Barkod')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('category.name')->label('Kategori')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('name')->label('Ürün')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('price')->label('Fiyat')->money('TRY', true),
                Tables\Columns\TextColumn::make('purchase_price')->label('Alış Fiyatı')->money('TRY', true),
                Tables\Columns\TextColumn::make('stock_type')->label('Stok Türü'),
                Tables\Columns\TextColumn::make('quantity')->label('Miktar'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Eklenme Tarihi'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Düzenle')->icon('heroicon-o-pencil'),
                Action::make('Print')
                    ->label('Barkod Yazdır')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn ($record) => route('barcode.print', $record))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                BulkAction::make('bulkPrint')
                    ->label('Toplu Yazdır')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->action(fn (Collection $records) => redirect()->route('barcode.bulk.print', [
                        'ids' => $records->pluck('id')->join(','),
                    ])),
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
            'index' => Pages\ListStocks::route('/'),
            'create' => Pages\CreateStock::route('/create'),
            'edit' => Pages\EditStock::route('/{record}/edit'),
        ];
    }
}
