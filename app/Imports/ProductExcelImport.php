<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductExcelImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            if ($index > 0) {
                if ($row['urun'] && $row['kategori']) {
                    $category = Category::where('name', $row['kategori'])->first();

                    if (!$category) {
                        $category = Category::create([
                            'name' => $row['kategori'],
                        ]);
                    }

                    if (!Product::where('name', $row['urun'])->exists()) {
                        $product = Product::create([
                            'name' => $row['urun'], // başlık isimlerine göre
                            'category_id' => $category->id,
                            'stock_type' => $row['stok_turu'],
                            'barcode' => $row['barkod'],
                            'quantity' =>  $row['stok_turu'] == 'Kilogram' ? $row['miktar'] /1000 : $row['miktar'],
                            'purchase_price' => $row['alis_fiyati'],
                            'slug' => $row['slug'],
                            'image' => $row['fotograf'],
                            'description' => $row['aciklama'],
                            'created_at' => $row['eklenme_tarihi'],
                        ]);

                        $productVariant = ProductVariant::create([
                            'product_id' => $product->id,
                            'price' => (float)str_replace('₺', '', $row['alis_fiyati']),
                            'type' => $row['stok_turu'],
                            'quantity' => 1,
                            'sort' => 1,
                        ]);
                    }
                }
            }
        }
    }
}

