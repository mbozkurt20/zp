<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Helpers\Pusher;
use App\Http\Resources\OrderCollection;
use App\Models\Order;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    public static function getNavigationLabel(): string
    {
        return 'Siparişler';
    }
    public static function getPluralModelLabel(): string
    {
        return 'Siparişler';
    }
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
               Tables\Columns\TextColumn::make('id')->label('Sipariş No')->sortable()->searchable(),
               Tables\Columns\TextColumn::make('barcode')->label('Barkod')->sortable()->searchable(),
               Tables\Columns\TextColumn::make('creator.name')->label('Ödeme Alan Kişi')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('user.name')->label('Müşteri'),
                Tables\Columns\TextColumn::make('total')->label('Toplam Tutar'),
                Tables\Columns\TextColumn::make('created_at')->label('Sipariş Tarihi')->sortable(),
                Tables\Columns\TextColumn::make('is_ready')->label('Sipariş Durumu')->formatStateUsing(fn ($state) => $state ? 'Hazır' : 'Hazırlanıyor...'),
            ])  ->recordUrl(fn () => null)
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('markAsReady')
                    ->label('Hazırlandı Yap')
                    ->requiresConfirmation()
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->action(function ($record) {
                        $record->is_ready = true;
                        $record->ready_date = \Carbon\Carbon::now(); // Tarihi ayarla
                        $record->save();

                        $orders = Order::whereDate('created_at',Carbon::today())->orderByDesc('is_ready')->get();
                        $orders = new OrderCollection($orders);

                        Pusher::trigger('orders-channel','orders-event', $orders);
                    })
                    ->visible(fn ($record) => !$record->is_ready),

                Action::make('Print')
                    ->label('Barkod Yazdır')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn ($record) => route('barcode.order.print', $record))
                    ->openUrlInNewTab(),

                Action::make('Print')
                    ->label('Fiş Yazdır')
                    ->icon('heroicon-o-printer')
                    ->color('warning')
                    ->url(fn ($record) => route('barcode.receipt.order.print', $record))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\BulkAction::make('markAsReadyBulk')
                        ->label('Seçilenleri Hazırla')
                        ->requiresConfirmation()
                        ->color('success')
                        ->icon('heroicon-o-check')
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                if (!$record->is_ready) {
                                    $record->is_ready = true;
                                    $record->ready_date = \Carbon\Carbon::now();
                                    $record->save();
                                }
                            });

                            $orders = Order::whereDate('created_at',Carbon::today())->orderByDesc('is_ready')->get();
                            $orders = new OrderCollection($orders);

                            Pusher::trigger('orders-channel','orders-event', $orders);
                        })
                        ->deselectRecordsAfterCompletion()
                        ->records(function (Collection $records) {
                            return $records->filter(fn ($record) => !$record->is_ready);
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
