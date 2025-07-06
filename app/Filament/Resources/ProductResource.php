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
use Filament\Tables\Actions\Action;
use Illuminate\Support\Collection;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    public static function getNavigationLabel(): string
    {
        return 'Ürünler';
    }
    public static function getModelLabel(): string
    {
        return __('Ürün');
    }
    public static function getPluralModelLabel(): string
    {
        return 'Ürünler';
    }
    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('image')
                    ->label('Vitrin Görseli')
                    ->directory('products')
                    ->required()
                    ->acceptedFileTypes(['image/jpeg','image/jpg', 'image/webp','image/avif','image/png',]),
                FileUpload::make('images')
                    ->label('Diğer Görseller')
                    ->directory('products')
                    ->multiple()
                    ->enableOpen()
                    ->nullable()
                    ->acceptedFileTypes(['image/jpeg','image/jpg', 'image/webp','image/avif','image/png', 'application/pdf']),
                Forms\Components\Select::make('category_id')->label('Kategori')->options(Category::pluck('name','id'))->required(),

                TextInput::make('name')->label('Ürün Adı')->required(),
                Forms\Components\TextInput::make('barcode')
                    ->label('Barkod')
                    ->default(function () {
                        do {
                            $barcode = 'p-' . rand(100000000, 999999999);
                        } while (\App\Models\Product::where('barcode', $barcode)->exists());
                        return $barcode;
                    })
                    ->required(),

                Forms\Components\Select::make('stock_type')->label('Stok Türü')->options([
                    'Kilogram' => 'Kilogram',
                    'Gram' => 'Gram',
                    'Litre' => 'Litre',
                    'Adet' => 'Adet',
                ])->required(),

                TextInput::make('quantity')
                    ->label('Stok Miktar')
                    ->required()
                    ->formatStateUsing(function ($state, $record) {
                        if ($record && $record->stock_type === 'Kilogram') {
                            return $state / 1000;
                        }
                        return $state;
                    })
                    ->dehydrateStateUsing(function ($state) {
                        // Virgülü noktaya çevir ve float yap
                        return floatval(str_replace(',', '.', $state));
                    }),

                TextInput::make('warning_quantity')
                    ->label('Uyarı Miktarı')
                    ->numeric()
                    ->nullable()
                    ->formatStateUsing(function ($state, $record) {
                        if ($record && $record->stock_type === 'Kilogram') {
                            return $state / 1000;
                        }
                        return $state;
                    }),

                Forms\Components\Textarea::make('description')->label('Ürün Açıklaması'),

                Forms\Components\Repeater::make('variants')
                    ->label('Ürün Varyantları')
                    ->relationship('variants') // Eloquent ilişkisi varsa direkt bağlanabilir.
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->label('Stok Türü')
                            ->options([
                                'Kilogram' => 'Kilogram',
                                'Gram' => 'Gram',
                                'Litre' => 'Litre',
                                'Adet' => 'Adet',
                            ])
                            ->required(),

                        TextInput::make('quantity')->label('Miktar')->numeric()->required(),
                        TextInput::make('price')->label('Fiyat')->numeric()->required(),
                    ])
                    ->columns(3)
                    ->orderable()
                    ->createItemButtonLabel('Varyant Ekle')
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('barcode')->label('Barkod')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('category.name')->label('Kategori')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('name')->label('Ürün')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('variants_price')
                    ->label('İlk Varyant Fiyatı')
                    ->getStateUsing(function ($record) {
                        return optional($record->variants->first())->price;
                    })
                    ->money('TRY', true),

                Tables\Columns\TextColumn::make('stock_type')->label('Stok Türü'),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Miktar')
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->stock_type === 'Kilogram') {
                            return $state / 1000;
                        }
                        return $state;
                    }),
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
                    ExportBulkAction::make('export')
                        ->label('Dışa Aktar')
                        ->icon('heroicon-o-arrow-down-tray'),
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
