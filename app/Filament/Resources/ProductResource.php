<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Category;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                TextInput::make('barcode')->label('Barkod')->nullable(),
                TextInput::make('name')->label('Ürün Adı')->required(),
                Forms\Components\Textarea::make('description')->label('Ürün Açıklaması')->required(),
                TextInput::make('price')
                    ->numeric()
                    ->label('Fiyat')
                    ->required(),
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
                TextInput::make('quantity')->minValue(1)->label('Miktar')->numeric()->required(),
                TextInput::make('warning_quantity')->label('Uyarı Miktarı')->numeric()->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
