<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Basket extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'user_id',
        'is_shopping',
        'is_checkout',
        'is_completed',
        'cart',
        'payment_type',
    ];

    public function basketItems(){
        return $this->hasMany(BasketItem::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->hasOne(Order::class);
    }
}
