<?php

namespace App\Http\Controllers;

use App\Models\Window;
use Illuminate\Http\Request;

class WindowController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $r
     * @return \Illuminate\Http\Response
     */
    public function store(Request $r)
    {
        try {
            $window = new Window;
            $window->user_id = $r->user_id;
            $window->product_id = $r->product_id;
            $window->save();
            $this->result = $window;
        } catch (QueryException $e) {
            $this->result['message'] = $e->getMessage();
            $this->statusCode = 500;
        }
        return response()->json($this->result, $this->statusCode);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $r
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $r)
    {
        try {
            $window = Window::where('user_id', $r->user_id)
                ->where('product_id', $r->product_id)
                ->first();
            $window->delete();
            $this->result = $window;
        } catch (QueryException $e) {
            $this->result['message'] = $e->getMessage();
            $this->statusCode = 500;
        }
        return response()->json($this->result, $this->statusCode);
    }
}
