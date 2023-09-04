<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Hash;

class UserController extends Controller
{
    public function login(Request $r)
    {
        try {
            $user = User::where('mail', $r->mail)
                ->where('pass', md5($r->pass))
                ->first();
            if ($user) {
                $user->token = Hash::make($user->mail . ':' . $user->pass);
                $user->save();
                $this->result = $user;
            } else {
                $this->result['message'] = 'Kullanıcı bulunamadı.';
                $this->statusCode = 500;
            }
        } catch (QueryException $e) {
            $this->result['message'] = $e->getMessage();
            $this->statusCode = 500;
        }
        return response()->json($this->result, $this->statusCode);
    }

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
            $users = User::orderBy($sort[0], $sort[1]);
            if (count($r->all()) > 1) {
                $users = $users->where('name', 'LIKE', "%{$r->name}%");
                foreach (['role', 'mail', 'phone', 'address'] as $name) {
                    if ($r->has($name) && $r->$name != '')
                        $users = $users->where($name, 'LIKE', "%{$r->$name}%");
                }
            }
            if (!$r->user->admin)
                $users = $users->where('role', 1);
            $users = $users->paginate(50);
            $this->result = UserResource::collection($users)->response()->getData(true);
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
            $user = new User;
            $user->role = $r->role;
            $user->name = $r->name;
            $user->mail = $r->mail;
            $user->pass = md5($r->pass);
            $user->phone = $r->phone;
            $user->address = $r->address;
            $user->admin = $r->has('admin') ? 1 : 0;
            $user->save();
            $this->result = $user;
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
            $user = User::findOrFail($id);
            $this->result = $user;
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
            $user = User::findOrFail($id);
            $user->role = $r->role;
            $user->name = $r->name;
            $user->mail = $r->mail;
            if ($r->pass)
                $user->pass = md5($r->pass);
            $user->phone = $r->phone;
            $user->address = $r->address;
            $user->admin = $r->has('admin') ? 1 : 0;
            $user->save();
            $this->result = $user;
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
            $user = User::findOrFail($id);
            $user->delete();
            $this->result = $user;
        } catch (QueryException $e) {
            $this->result['message'] = $e->getMessage();
            $this->statusCode = 500;
        }
        return response()->json($this->result, $this->statusCode);
    }
}