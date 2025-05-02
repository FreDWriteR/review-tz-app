<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    // Если нужно массовое заполнение:
    protected $fillable = [
        'uuid',
        'is_active',
        'category',
        'name',
        'description',
        'thumbnail',
        'price',
    ];
}
