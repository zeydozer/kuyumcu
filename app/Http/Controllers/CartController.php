<?php

namespace App\Http\Controllers;

use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $r)
    {
        try {
            $carts = Cart::where('user_id', $r->user->id)
                ->with('product:id,name,type,photo,weight', 'height')
                ->whereHas('product', function ($q) use ($r) {
                    $q->where('type', $r->type);
                })
                ->orderBy('created_at', 'DESC')
                ->get();
            $resource['data'] = CartResource::collection($carts);
            $resource['total'] = [
                'quantity' => number_format($carts->sum('quantity'), 0, ',', '.'),
                'weight' => number_format($carts->sum('weight_total'), 2, ',', '.'),
            ];
            $this->result = $resource;
        } catch (QueryException $e) {
            $this->result['message'] = $e->getMessage();
            $this->statusCode = 500;
        }
        return response()->json($this->result, $this->statusCode);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $r
     * @return \Illuminate\Http\Response
     */
    public function store(Request $r)
    {
        $r->validate([
            'photo' => 'image|file'
        ]);
        try {
            $control = $r->has('fast') ? true : $this->quantity($r->height);
            if (!$control) {
                $this->statusCode = 500;
                $this->result['message'] = 'Adet girilmedi.';
            } else {
                $cart = new Cart;
                $cart->user_id = $r->user->id;
                $cart->product_id = $r->id;
                $cart->width = $r->width;
                $cart->weight = $r->weight;
                if ($r->hasFile('photo')) {
                    $photoName = mt_rand() . '.jpg';
                    if ($r->photo->move(public_path('img/cart'), $photoName))
                        $cart->photo = $photoName;
                }
                $cart->note = $r->note;
                $cart->quantity = $r->quantity;
                $cart->weight_total = $r->weight_total;
                $cart->save();
                $heights = ['cart_id' => $cart->id];
                foreach ($r->height as $height => $quantity) {
                    $column = "height_$height";
                    $heights[$column] = $quantity;
                }
                $cart->height()->forceCreate($heights);
                $cart->user = User::where('id', $cart->user_id)->withCount('carts')->first();
                $this->result = $cart;
            }
        } catch (QueryException $e) {
            $this->result['message'] = $e->getMessage();
            $this->statusCode = 500;
        }
        return response()->json($this->result, $this->statusCode);
    }

    public function quantity($heights)
    {
        $control = true;
        $totalQuantity = 0;
        foreach ($heights as $height => $quantity) {
            if ($quantity < 0) {
                $control = false;
                break;
            }
            $totalQuantity += $quantity;
        }
        if ($totalQuantity <= 0)
            $control = false;
        return $control;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $cart = Cart::where('id', $id)
                ->with('product:id,name,type,photo', 'height')
                ->orderBy('created_at', 'DESC')
                ->first();
            $this->result = new CartResource($cart);
        } catch (QueryException $e) {
            $this->result['message'] = $e->getMessage();
            $this->statusCode = 500;
        }
        return response()->json($this->result, $this->statusCode);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $r
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $r, $id)
    {
        $r->validate([
            'photo' => 'image|file'
        ]);
        try {
            $control = 
                $r->has('splice') ? 
                true :
                $this->quantity($r->height);
            if (!$control) {
                $this->statusCode = 500;
                $this->result['message'] = 'Adet girilmedi.';
            } else {
                $cart = Cart::findOrFail($id);
                $cart->user_id = $r->user->id;
                $cart->product_id = $r->product_id;
                $cart->width = $r->width;
                $cart->weight = $r->weight;
                if ($r->has('photo-del')) {
                    /* if ($cart->photo && file_exists(public_path("img/cart/{$cart->photo}")))
                        unlink(public_path("img/cart/{$cart->photo}")); */
                    $cart->photo = null;
                } else if ($r->hasFile('photo') || $r->has('photo')) {
                    $photoName = mt_rand() . '.jpg';
                    if ($r->photo->move(public_path('img/cart'), $photoName)) {
                        /* if ($cart->photo && file_exists(public_path("img/cart/{$cart->photo}")))
                            unlink(public_path("img/cart/{$cart->photo}")); */
                        $cart->photo = $photoName;
                    }
                }
                $cart->note = $r->note;
                $cart->quantity = $r->quantity;
                $cart->weight_total = 
                    $r->has('fast_weight') ?
                    $r->fast_weight :
                    $r->weight_total;
                $cart->save();
                $heights = [];
                foreach ($r->height as $height => $quantity) {
                    $column = "height_$height";
                    $heights[$column] = $quantity;
                }
                $cart->height()->update($heights);
                $cart->product = Product::find($cart->product_id);
                $this->result = $cart;
            }
        } catch (QueryException $e) {
            $this->result['message'] = $e->getMessage();
            $this->statusCode = 500;
        }
        return response()->json($this->result, $this->statusCode);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $cart = Cart::where('id', $id)->with('product', 'user')->first();
            $cart->delete();
            $this->result = Cart::onlyTrashed()->where('id', $id)->with('product', 'user')->first();
        } catch (QueryException $e) {
            $this->result['message'] = $e->getMessage();
            $this->statusCode = 500;
        }
        return response()->json($this->result, $this->statusCode);
    }
}