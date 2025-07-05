<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use App\Imports\ProductExcelImport;
use Maatwebsite\Excel\Facades\Excel;

class ProductImport extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $title = 'Excel ile Ürün Yükle';
    protected static string $view = 'filament.pages.product-import';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('file')
                    ->label('Excel Dosyası')
                    ->disk('local')
                    ->directory('imports')
                    ->acceptedFileTypes([
                        'application/vnd.ms-excel',            // .xls
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
                        '.xls',
                        '.xlsx'
                    ])
                    ->required()
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $file = reset($this->data['file']);
        $path = $file->getPathname();

        // dd($this->data['file']); // Debug için gerek kalmadı

        try {
            Excel::import(new ProductExcelImport(), $path);

            Notification::make()
                ->title('Başarılı!')
                ->body('Ürünler başarıyla yüklendi.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Hata!')
                ->body('Yükleme sırasında hata oluştu: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
}
