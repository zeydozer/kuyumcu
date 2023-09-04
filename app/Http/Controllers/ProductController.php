<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Window;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $r)
    {
        try {
            $products = app("\\App\\Models\\Product");
            $windowsId = Window::where('user_id', $r->has('user_id') ? $r->user_id : $r->user->id)->pluck('product_id')->toArray();
            if (count($windowsId) > 0)
                $products = $products->orderByRaw('FIELD(products.id, '. implode(', ', $windowsId) .') DESC');
            if (!$r->has('order'))
                $r->merge(['order' => ['created_at', 'DESC']]);
            $products = $products->orderBy($r->order[0], $r->order[1]);
            if (count($r->all()) > 1) {
                $products = $products->where('name', 'LIKE', "%{$r->name}%");
                if ($r->has('ctg') && $r->ctg != '') {
                    $ctg = Category::where('id', $r->ctg)->first();
                    $ctgIds = $ctg->childsId();
                    array_push($ctgIds, $r->ctg);
                    $products = $products->whereIn('ctg_id', $ctgIds);
                }
            }
            $this->result = $products->withOnly('windows')->paginate(30);
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
            $product = new Product;
            $product->name = $r->name;
            if ($r->has('ctg_id'))
                $product->ctg_id = $r->ctg_id;
            $product->width = $r->width;
            $product->weight = $r->weight;
            $product->between = $r->between;
            if ($r->hasFile('photo')) {
                $photoName = mt_rand() .'.jpg';
                if ($r->photo->move(public_path('img/product'), $photoName))
                    $product->photo = $photoName;
            }
            if ($r->has('empty'))
                $product->empty = 1;
            $product->save();
            $this->result = $product;
        } catch (QueryException $e) {
            $this->result['message'] = $e->getMessage();
            $this->statusCode = 500;
        }
        return response()->json($this->result, $this->statusCode);
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
            $product = Product::where('id', $id)
                ->withOnly('category')
                ->first();
            $this->result = $product;
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
            $product = Product::findOrFail($id);
            $product->name = $r->name;
            $product->ctg_id = 
                $r->has('ctg_id') ?
                $r->ctg_id :
                null;
            $product->width = $r->width;
            $product->weight = $r->weight;
            $product->between = $r->between;
            if ($r->has('photo-del')) {
                /* if ($product->photo && file_exists(public_path("img/product/{$product->photo}")))
                    unlink(public_path("img/product/{$product->photo}")); */
                $product->photo = null;
            } else if ($r->hasFile('photo')) {
                $photoName = mt_rand() .'.jpg';
                if ($r->photo->move(public_path('img/product'), $photoName)) {
                    /* if ($product->photo && file_exists(public_path("img/product/{$product->photo}")))
                        unlink(public_path("img/product/{$product->photo}")); */
                    $product->photo = $photoName;
                }
            }
            $product->empty = $r->has('empty') ? 1 : 0;
            $product->save();
            $this->result = $product;
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
            $product = Product::findOrFail($id);
            $product->delete();
            $this->result = $product;
        } catch (QueryException $e) {
            $this->result['message'] = $e->getMessage();
            $this->statusCode = 500;
        }
        return response()->json($this->result, $this->statusCode);
    }
}
