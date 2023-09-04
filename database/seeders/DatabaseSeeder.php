<?php

namespace Database\Seeders;

use App\Models\Bracelets;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Category::factory(100)->create();
        // Product::factory(10000)->create();
        // User::factory(100)->create();
        /* Cart::factory(10)->create();
        Bracelets::factory(10)->create(); */
        // Order::factory(5000)->create();
        /* $carts = Cart::where('user_id', 1)->get();
        foreach ($carts as $cart)
            Bracelets::onlyTrashed()->where('cart_id', $cart->id)->restore(); */
        /* $carts = Cart::withTrashed()->with('product', 'height')->whereBetween('id', [140001, 150000])->get();
        foreach ($carts as $cart) {
            $order_id = rand(1, 20000);
            $array = $cart->makeHidden('created_at', 'updated_at', 'deleted_at', 'product', 'height')->toArray();
            $orderCart = \App\Models\OrderCart::forceCreate(array_merge($array, ['order_id' => $order_id]));
            $product = $cart->product->makeHidden('created_at', 'updated_at', 'deleted_at')->toArray();
            \App\Models\OrderProduct::forceCreate(array_merge($product, ['order_id' => $order_id]));
            $height = $cart->height->makeHidden('created_at', 'updated_at', 'deleted_at')->toArray();
            $orderCart->height()->forceCreate(array_merge($height, ['cart_id' => $cart->id]));
            $orderCart->height()->update(['cart_id' => $cart->id]);
            $cart->delete();
        } */
        $carts = \App\Models\Cart::withTrashed()->whereBetween('id', [150000, 160000])->get();
        foreach ($carts as $cart) {
            $quantity = 0;
            $height = $cart->height()->withTrashed()->first();
            for ($i = 56; $i <= 74; $i += 2)
                $quantity += $height->{"height_$i"};
            $cart->quantity = $quantity;
            $cart->weight_total = $quantity * $cart->weight;
            $cart->save();
        }
    }
}
