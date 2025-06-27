<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Category extends Model
{
    use SoftDeletes;
    protected static function booted()
    {
        static::saving(function ($category) {
            if (empty($category->slug) && !empty($category->name)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }
    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
    ];

    public function products(){
        return $this->hasMany(Product::class);
    }
}
