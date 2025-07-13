<?php

namespace App\Filament\Pages;

use App\Models\Category;
use App\Models\Product;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Livewire\WithPagination;

class CustomJsPage extends Page
{
    use WithPagination;

    protected static ?string $navigationIcon = 'heroicon-o-tv';
    protected static string $view = 'filament.pages.custom-js-page';
    protected static ?string $title = 'Sipariş Ekranı';

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    public static function getNavigationLabel(): string
    {
        return 'Sipariş Ekranı';
    }

    public function getCategoriesProperty(): Collection
    {
        return Category::with(['products:id,name,barcode,category_id'])->get();
    }
}
