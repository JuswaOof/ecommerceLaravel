<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'userId', 'productId', 'productName', 'customer', 'status', 'price'
    ];
}
