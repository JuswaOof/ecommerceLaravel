<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'productName', 'productDescription', 'imageUpload', 'isAvailable', 'price', 'isFeatured', 'category'
    ];
}
