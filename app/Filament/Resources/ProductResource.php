<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Category;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Collection;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    public static function getNavigationLabel(): string
    {
        return 'Ürünler';
    }
    public static function getPluralModelLabel(): string
    {
        return 'Ürünler';
    }
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('image')
                    ->label('Vitrin Görseli')
                    ->directory('products')
                    ->required()
                    ->acceptedFileTypes(['image/jpeg','image/jpg', 'image/webp','image/avif','image/png', 'application/pdf']),
                FileUpload::make('images')
                    ->label('Diğer Görseller')
                    ->directory('products')
                    ->multiple()
                    ->enableOpen()
                    ->nullable()
                    ->acceptedFileTypes(['image/jpeg','image/jpg', 'image/webp','image/avif','image/png', 'application/pdf']),
                TextInput::make('name')->label('Ürün Adı')->required(),
                Forms\Components\Textarea::make('description')->label('Ürün Açıklaması')->required(),
                TextInput::make('price')
                    ->numeric()
                    ->label('Fiyat')
                    ->required(),

                Forms\Components\TextInput::make('barcode')
                    ->label('Barkod')
                    ->default(function () {
                        do {
                            $barcode = 'p-' . rand(100000000, 999999999);
                        } while (\App\Models\Product::where('barcode', $barcode)->exists());
                        return $barcode;
                    })
                    ->required(),
                Forms\Components\Select::make('sales_quantity')->label('Ürün Satış Miktarı')->options([
                    '100 Gram' => '100 Gram',
                    '125 Gram' => '125 Gram',
                    '150 Gram' => '150 Gram',
                    '200 Gram' => '200 Gram',
                    '250 Gram' => '250 Gram',
                    '500 Gram' => '500 Gram',
                    '1 KG' => '1 KG',
                ]),
                TextInput::make('discount')
                    ->numeric()
                    ->default(0.00)
                    ->label('İndirim Tutarı')
                    ->required(),
                Forms\Components\Select::make('stock_type')->label('Stok Türü')->options([
                    'Kilogram' => 'Kilogram',
                    'Gram' => 'Gram',
                    'Litre' => 'Litre',
                    'Adet' => 'Adet',
                ])->required(),
                Forms\Components\Select::make('category_id')->label('Kategori')->options(Category::pluck('name','id'))->required(),
                TextInput::make('quantity')->minValue(1)->label('Stok Miktar')->numeric()->required(),
                TextInput::make('warning_quantity')->label('Uyarı Miktarı')->numeric()->nullable(),
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
                Tables\Columns\TextColumn::make('discount')->label('İndirim Tutarı')->money('TRY', true),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
