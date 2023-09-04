<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    public static function boot()
    {
        parent::boot();

        static::deleted(function ($item) {
            $windows = $item->windows()->get();
            foreach ($windows as $window)
                $window->delete();
        });
    }

    public function category()
    {
        return $this->hasOne(Category::class, 'id', 'ctg_id');
    }

    public function windows()
    {
        return $this->hasMany(Window::class, 'product_id', 'id');
    }
}
