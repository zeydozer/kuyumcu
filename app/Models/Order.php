<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    public static function boot()
    {
        parent::boot();

        static::deleted(function ($item) {
            $carts = $item->carts()->get();
            foreach ($carts as $cart)
                $cart->delete();
        });
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function auth()
    {
        return $this->hasOne(User::class, 'id', 'auth_id');
    }

    public function carts()
    {
        return $this->hasMany(OrderCart::class, 'order_id', 'id');
    }

    public function products()
    {
        return $this->hasMany(OrderProduct::class, 'order_id', 'id');
    }
}