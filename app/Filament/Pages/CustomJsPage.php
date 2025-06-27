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

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.custom-js-page';
   protected static ?string $title = 'Sipariş Ekranı';
    public static function shouldRegisterNavigation(): bool
    {
        return true; // Menüde gözüksün
    }

    public static function getNavigationLabel(): string
    {
        return 'Sipariş Ekranı';
    }

    public function getCategoriesProperty(): Collection
    {
        return Category::with('products')->get();
    }

    public function filteredProducts($categoryId)
    {
        $query = strtolower($this->searchQueries[$categoryId] ?? '');

        return Product::where('category_id', $categoryId)
            ->when($query, function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('name', 'like', "%$query%")
                        ->orWhere('description', 'like', "%$query%");
                });
            })->get();
    }
}
