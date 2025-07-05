<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BasketItem extends Model
{
   use HasFactory;

   protected $fillable = [
       'basket_id',
       'product_id',
       'product_variant_id',
       'quantity'
   ];

   public function product()
   {
       return $this->belongsTo(Product::class);
   }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
