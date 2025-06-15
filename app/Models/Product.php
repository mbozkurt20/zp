<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory,SoftDeletes;
    protected static function booted()
    {
        static::saving(function ($product) {
            if (empty($product->slug) && !empty($product->name)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    protected $casts = [
        'images' => 'array',
    ];
    protected $fillable = [
      'category_id',
      'name',
      'slug',
      'image',
      'description',
      'price',
      'discount',
      'tax',
      'qr_code',
      'barcode',
      'stock_type',
      'quantity',
      'warning_quantity',
    ];
}
