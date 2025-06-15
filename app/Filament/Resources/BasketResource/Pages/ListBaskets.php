<?php

namespace App\Filament\Resources\BasketResource\Pages;

use App\Filament\Resources\BasketResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBaskets extends ListRecords
{
    protected static string $resource = BasketResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
