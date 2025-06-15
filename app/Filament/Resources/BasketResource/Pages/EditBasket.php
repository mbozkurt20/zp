<?php

namespace App\Filament\Resources\BasketResource\Pages;

use App\Filament\Resources\BasketResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBasket extends EditRecord
{
    protected static string $resource = BasketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
