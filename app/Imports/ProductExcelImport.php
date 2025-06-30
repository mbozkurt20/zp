<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductExcelImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $index =>  $row) {
            if ($index > 0){
                $category = Category::where('name',$row['kategorisi'])->first();

                $barcode = 'p-' . rand(100000000, 999999999);

                if (Product::where('barcode', $barcode)->exists()) {
                    $barcode = 'p-' . rand(100000000, 999999999);
                }

                if (!$category){
                    $category = Category::create([
                        'name' => $row['kategorisi'],
                    ]);
                }

               if (!Product::where('name', $row['urun_adi'])->exists()) {
                  $product = Product::create([
                       'name' => $row['urun_adi'], // başlık isimlerine göre
                       'category_id' => $category->id,
                       'barcode' => $barcode,
                       'price' => $row['fiyat_tl'],
                   ]);
               }
            }
        }
    }
}

