<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Inertia\Testing\Concerns\Has;

class Order extends Model
{
    use SoftDeletes, HasFactory;

    protected $casts = [
        'total' => 'float',
    ];
    protected $fillable = [
        'creator_id',
        'barcode',
        'user_id',
        'basket_id',
        'discount',
        'is_paid',
        'total',
        'is_ready',
        'ready_date',
    ];

    public function basket()
    {
        return $this->belongsTo(Basket::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
