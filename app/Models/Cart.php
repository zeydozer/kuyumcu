<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cart extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['product_id'];

    public static $productType;

    public static function boot()
    {
        parent::boot();

        static::deleted(function ($item) {
            $item->height()->delete();
        });

        static::retrieved(function ($item) {
            self::$productType = $item->product->type;
        });
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id')->withCount('carts');
    }

    public function height()
    {
        $type = ucfirst(self::$productType ?? $this->product()->value('type'));
        return $this->hasOne(app("\\App\\Models\\{$type}"), 'cart_id', 'id');        
    }

    public function orderCart()
    {
        return $this->belongsToMany(OrderCart::class, 'carts_order_carts', 'cart_id', 'order_cart_id');
    }
}