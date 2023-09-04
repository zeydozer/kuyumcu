<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use HasFactory, SoftDeletes;

    public static function boot()
    {
        parent::boot();

        static::deleted(function ($item) {
            $orders = $item->orders()->get();
            foreach ($orders as $order)
                $order->delete();
            $windows = $item->windows()->get();
            foreach ($windows as $window)
                $window->delete();
        });
    }

    public function carts()
    {
        return $this->hasMany(Cart::class, 'user_id', 'id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id', 'id');
    }

    public function windows()
    {
        return $this->hasMany(Window::class, 'user_id', 'id');
    }
}
