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
                $slug = Str::slug($product->name);
                $originalSlug = $slug;
                $count = 1;

                // Benzersiz slug olana kadar kontrol et
                while (static::where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $count++;
                }

                $product->slug = $slug;
            }
        });
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
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
      'is_day',
      'is_favourite',
      'file',
      'image',
      'description',
    ];
}
