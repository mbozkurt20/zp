<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
      'slug',
      'is_day',
      'is_favourite',
      'image',
      'description',
      'type',
    ];
}
