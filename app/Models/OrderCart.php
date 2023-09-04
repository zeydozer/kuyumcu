<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderCart extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['*'];

    public static $productType;

    public static function boot()
    {
        parent::boot();

        static::deleted(function ($item) {
            $item->height()->delete();
            $item->product()->delete();
        });

        static::retrieved(function ($item) {
            self::$productType = $item->product->type;
        });
    }

    public function product()
    {
        return $this->hasOne(OrderProduct::class, 'id', 'product_id')
            ->where('order_id', $this->order_id);
    }

    public function height()
    {
        $type = ucfirst(self::$productType ?? $this->product()->value('type'));
        return $this->hasOne(app("\\App\\Models\\Order{$type}"), 'cart_id', 'id');
    }
}
