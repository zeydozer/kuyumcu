<?php

namespace App\Http\Controllers;

use App\Http\Controllers\CartController;
use App\Http\Resources\OrderResource;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;
use DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $r)
    {
        try {
            $sort = 
                $r->has('sort') && $r->sort ?
                explode(' ', $r->sort) : 
                ['created_at', 'DESC'];
            $orders = Order::with('user:id,name', 'auth:id,name')->orderBy($sort[0], $sort[1]);
            if ($r->user->role == 1)
                $orders = $orders->where('user_id', $r->user->id);
            if (count($r->all()) > 1) {
                if ($r->has('status') && $r->status != '')
                    $orders = $orders->where('status', $r->status);
                foreach (['created_at', 'finished_at', 'id', 'quantity', 'weight'] as $name) {
                    if (is_array($r->$name)) {
                        $count = count(array_filter($r->$name));
                        if ($r->has($name) && $count > 0) {
                            $orders =
                                $count == 2 ?
                                $orders->whereBetween($name, $r->$name) :
                                $orders->where($name, $r->{$name}[0] ? '>=' : '<=', $r->{$name}[0] ?? $r->{$name}[1]);
                        }
                    }
                }
                if ($r->has('note') && $r->note != '')
                    $orders = $orders->where('note', 'LIKE', "%{$r->note}%");
                foreach (['auth_name', 'user_name'] as $name) {
                    if ($r->has($name) && $r->$name) {
                        $orders = $orders->whereHas(str_replace('_name', null, $name), function ($q) use ($r, $name) {
                            $q->where('name', 'LIKE', "%{$r->$name}%");
                        });
                    }
                }
            }
            $resource = OrderResource::collection($orders->paginate(50))->response()->getData(true);
            $resource['total'] = [
                'quantity' => number_format($orders->sum('quantity'), 0, ',', '.'),
                'weight' => number_format($orders->sum('weight'), 2, ',', '.'),
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
        try {
            $result = $this->save(new Order, $r);
            $this->result = $result->getData();
            $this->statusCode = $result->getStatusCode();
        } catch (QueryException $e) {
            $this->result['message'] = $e->getMessage();
            $this->statusCode = 500;
        }
        return response()->json($this->result, $this->statusCode);
    }

    public function save(Order $order, Request $r)
    {
        try {
            if ($r->has('fast')) {
                $result = $this->cartUpdate($order, $r);
                if (!$result)
                    goto result;
            }
            $order->auth_id = $order->auth_id ?? $r->user->id;
            $order->user_id = 
                $r->has('user_id') ?
                $r->user_id :
                $r->user->id;
            $order->note = $r->note;
            $order->quantity = $r->quantity;
            $order->weight = $r->weight;
            $order->finished_at = $r->finished_at;
            $order->save();
            if ($r->has('fast')) {
                $order->delete();
                $order->deleted_at = null;
                $order->save();
            }
            $carts = $order->auth()->first()->carts()->with('product', 'height')->get();
            foreach ($carts as $cart) {
                $array = $cart->makeHidden('id', 'created_at', 'updated_at', 'deleted_at', 'product', 'height')->toArray();
                $orderCart = $order->carts()->forceCreate($array);
                $product = $cart->product->makeHidden('created_at', 'updated_at', 'deleted_at')->toArray();
                $order->products()->forceCreate($product);
                $height = $cart->height->makeHidden('id', 'created_at', 'updated_at', 'deleted_at')->toArray();
                $orderCart->height()->forceCreate($height);
                $orderCart->height()->update(['cart_id' => $orderCart->id]);
                $cart->delete();
            }
            $this->result = $order;
        } catch (QueryException $e) {
            $this->result['message'] = $e->getMessage();
            $this->statusCode = 500;
        }
        result: return response()->json($this->result, $this->statusCode);
    }

    public function cartUpdate(Order $order, Request $r) 
    {
        $controller = new CartController;
        foreach ($r->cart as $id => $cart) {
            $rCart = new Request($cart);
            $rCart->merge(['user' => $r->user]);
            if ($r->has('splice'))
                $rCart->merge(['splice' => true]);
            $result = $controller->update($rCart, $id);
            if ($result->getStatusCode() == 500) {
                $this->statusCode = $result->getStatusCode();
                $this->result = $result->getData();
                return false;
            }
        }
        return true;
    }

    public function splice(Order $order, Request $r)
    {
        try {
            $r->merge(['quantity' => 0]);
            $r->merge(['weight' => 0]);
            $r->merge(['splice' => true]);
            if (!$this->cartUpdate($order, $r))
                goto result;
            /* $this->statusCode = 500;
            $this->result['message'] = 'test';
            goto result; */
            $carts = $r->user->carts()->get();
            foreach ($carts as $i => $cart) {
                $orderCart = $cart->orderCart()->first();
                $order->weight -= $orderCart->weight_total;
                $orderCart->photo = $cart->photo;
                $orderCart->note = $cart->note;
                $orderCart->width = $cart->width;
                $orderCart->weight = $cart->weight;
                $orderCart->weight_total = $orderCart->weight * $orderCart->quantity;
                $orderCart->save();
                $order->weight += $orderCart->weight_total;
                $order->save();
                $cartHeight = array_filter(
                    $cart->height()->first()->toArray(),
                    fn ($name) => strpos($name, 'height') !== false,
                    ARRAY_FILTER_USE_KEY
                );
                $orderCartHeight = array_filter(
                    $orderCart->height()->first()->toArray(),
                    fn ($name) => strpos($name, 'height') !== false,
                    ARRAY_FILTER_USE_KEY
                );
                $heightDiff = array_map(function ($x, $y) {
                    return $x - $y;
                } , $orderCartHeight, $cartHeight);
                $heightDiffSum = array_sum($heightDiff);
                if ($heightDiffSum > 0) {
                    $keys = array_keys($orderCartHeight);
                    $cartHeight = $cart->height()->first();
                    $orderCartHeight = $orderCart->height()->first();
                    foreach ($heightDiff as $j => $diff) {
                        $cartHeight->{"{$keys[$j]}"} = $diff;
                        $orderCartHeight->{"{$keys[$j]}"} -= $diff;
                    }
                    $cartHeight->save();
                    $cart->quantity = $heightDiffSum;
                    $r->merge(['quantity' => $r->quantity + $cart->quantity]);
                    $cart->weight_total = $cart->weight * $cart->quantity;
                    $r->merge(['weight' => $r->weight + $cart->weight_total]);
                    $cart->save();
                    if ($cart->quantity == $orderCart->quantity)
                        $orderCart->delete();
                    else {
                        $orderCartHeight->save();
                        $orderCart->quantity -= $cart->quantity;
                        $orderCart->weight_total -= $cart->weight_total;
                        $orderCart->save();
                    }
                    $order->quantity -= $cart->quantity;
                    $order->weight -= $cart->weight_total;
                    $order->save();
                } else 
                    $cart->delete();
            }
            if ($r->user->carts()->count() > 0) {
                $result = $this->save(new Order, new Request($r->except(['fast', 'id'])));
                $this->result = $result->getData();
                $this->statusCode = $result->getStatusCode();
            }
        } catch (QueryException $e) {
            $this->result['message'] = $e->getMessage();
            $this->statusCode = 500;
        }
        result: return response()->json($this->result, $this->statusCode);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $r)
    {
        try {
            $order = Order::where('id', $id)
                ->with('carts', 'user')
                ->first();
            foreach ($order->carts as $i => $cart) {
                $cart->product->type = ucfirst($cart->product->type);
                $cart->height = app("\\App\\Models\\Order{$cart->product->type}")
                    ->where('cart_id', $cart->id)
                    ->first();
            }
            $this->result = new OrderResource($order);
            if ($r->has('fast'))
                $this->cartClone($order, $r);
        } catch (QueryException $e) {
            $this->result['message'] = $e->getMessage();
            $this->statusCode = 500;
        }
        return response()->json($this->result, $this->statusCode);
    }

    public function cartClone(Order $order, Request $r)
    {
        $carts = $r->user->carts()->get();
        foreach ($carts as $cart)
            $cart->delete();
        foreach ($order->carts()->with('product', 'height')->get() as $orderCart) {
            $array = $orderCart->makeHidden('id', 'order_id', 'created_at', 'updated_at', 'deleted_at', 'product', 'height')->toArray();
            $cart = $r->user->carts()->forceCreate($array);
            Cart::where('id', $cart->id)->update(['user_id' => $r->user->id]);
            $height = $orderCart->height->makeHidden('id', 'created_at', 'updated_at', 'deleted_at')->toArray();
            $cart->height()->forceCreate($height);
            $cart->height()->update(['cart_id' => $cart->id]);
            DB::table('carts_order_carts')->insert([
                'cart_id' => $cart->id,
                'order_cart_id' => $orderCart->id
            ]);
        }
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
        try {
            if ($id == 0) {
                Order::whereIn('id', $r->ids)
                    ->update([
                        'status' => $r->status
                    ]);
            } else if ($r->has('fast')) {
                $order = Order::findOrFail($id);
                $result = 
                    $r->submit == 'Kaydet' ?
                    $this->save($order, $r) :
                    $this->splice($order, $r);
                $this->result = $result->getData();
                $this->statusCode = $result->getStatusCode();
            } else {
                $order = Order::findOrFail($id);
                $order->status = $r->status;
                $order->save();
                $this->result = new OrderResource($order);
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
            $order = Order::findOrFail($id);
            $order->delete();
            $this->result = $order;
        } catch (QueryException $e) {
            $this->result['message'] = $e->getMessage();
            $this->statusCode = 500;
        }
        return response()->json($this->result, $this->statusCode);
    }

    public function report(Request $r)
    {
        try {
            $sort = 
                $r->has('sort') && $r->sort ?
                explode(' ', $r->sort) : 
                ['created_at', 'DESC'];
            $orders = Order::orderBy($sort[0], $sort[1])
                ->select('id', 'user_id')
                ->with('user:id,name', 'carts');
            if ($r->user->role == 1)
                $orders = $orders->where('user_id', $r->user->id);
            if (count($r->all()) > 1) {
                if ($r->has('status') && $r->status != '')
                    $orders = $orders->where('status', $r->status);
                foreach (['created_at', 'finished_at', 'id', 'quantity', 'weight'] as $name) {
                    if (is_array($r->$name)) {
                        $count = count(array_filter($r->$name));
                        if ($r->has($name) && $count > 0) {
                            $orders =
                                $count == 2 ?
                                $orders->whereBetween($name, $r->$name) :
                                $orders->where($name, $r->{$name}[0] ? '>=' : '<=', $r->{$name}[0] ?? $r->{$name}[1]);
                        }
                    }
                }
                if ($r->has('note') && $r->note != '')
                    $orders = $orders->where('note', 'LIKE', "%{$r->note}%");
                foreach (['auth_name', 'user_name'] as $name) {
                    if ($r->has($name) && $r->$name) {
                        $orders = $orders->whereHas(str_replace('_name', null, $name), function ($q) use ($r, $name) {
                            $q->where('name', 'LIKE', "%{$r->$name}%");
                        });
                    }
                }
            }
            $resource['total'] = [
                'quantity' => number_format($orders->sum('quantity'), 0, ',', '.'),
                'weight' => number_format($orders->sum('weight'), 2, ',', '.'),
            ];
            $orders = $orders->get();
            foreach ($orders as $order) {
                foreach ($order->carts as $cart) {
                    $cart->product->type = ucfirst($cart->product->type);
                    $cart->height = app("\\App\\Models\\Order{$cart->product->type}")
                        ->where('cart_id', $cart->id)
                        ->first();
                }
            }
            $resource['data'] = $orders;
            $type = app("\\App\\Http\\Controllers\\{$r->type}Report");
            $resource['report'] = OrderReport::getResults($type, $resource['data']);
            $this->result = $resource;
        } catch (QueryException $e) {
            $this->result['message'] = $e->getMessage();
            $this->statusCode = 500;
        }
        return response()->json($this->result, $this->statusCode);
    }
}

class OrderReport
{
    static function getResults(ReportType $type, $orders)
    {
        return $type->setResults($orders);
    }
}

interface ReportType
{
    public function setResults($orders);
}

class ProductReport implements ReportType
{
    // override
    public function setResults($orders) 
    {
        $reports = [];
        foreach ($orders as $order) {
            foreach ($order->carts as $cart)
                $reports[$cart->width][strtolower($cart->product->type)][$cart->product->id]["{$cart->weight}"][] = $cart;
        }
        return $reports;
    }
}

class CustomerReport implements ReportType
{
    // override
    public function setResults($orders) 
    {
        $reports = [];
        foreach ($orders as $order) {
            foreach ($order->carts as $cart)
                $reports[$order->user->id][$order->user->name][strtolower($cart->product->type)][$cart->product->id]["{$cart->weight}"][$cart->width][] = $cart;
        }
        return $reports;
    }
}