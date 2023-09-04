<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Cache;

class CategoryController extends Controller
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
            $categories = Category::withOnly('root')->orderBy($sort[0], $sort[1]);
            if ($r->has('product_count'))
                $categories = $categories->withCount('products');
            if ($r->has('name') && $r->name != '')
                $categories = $categories->where('name', 'LIKE', "%{$r->name}%");
            if ($r->has('root_name') && $r->root_name != '') {
                $categories = $categories->whereHas('root', function ($q) use ($r) {
                    $q->where('name', 'LIKE', "%{$r->root_name}%");
                });
            }
            if ($r->has('root_id'))
                $categories = $categories->where('root_id', $r->root_id);
            $categories = $categories->paginate(50);
            $this->result = CategoryResource::collection($categories)->response()->getData(true);
        } catch (QueryException $e) {
            $this->result['message'] = $e->getMessage();
            $this->statusCode = 500;
        }
        result: return response()->json($this->result, $this->statusCode);
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
            $ctg = new Category;
            $ctg->name = $r->name;
            if ($r->has('root_id'))
                $ctg->root_id = $r->root_id;
            $ctg->save();
            $this->result = new CategoryResource($ctg);
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
            $ctg = Category::where('id', $id)->withOnly('root')->first();
            $this->result = new CategoryResource($ctg);
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
        try {
            if ($id == $r->root_id) {
                $this->result['message'] = 'Üst kategori başka kategori olmalı.';
                $this->statusCode = 500;
            } else {
                $ctg = Category::findOrFail($id);
                $ctg->name = $r->name;
                $ctg->root_id = 
                    $r->has('root_id') ?
                    $r->root_id :
                    null;
                $ctg->save();
                $this->result = new CategoryResource($ctg);
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
            $ctg = Category::findOrFail($id);
            $ctg->delete();
            $this->result = new CategoryResource($ctg);
        } catch (QueryException $e) {
            $this->result['message'] = $e->getMessage();
            $this->statusCode = 500;
        }
        return response()->json($this->result, $this->statusCode);
    }
}