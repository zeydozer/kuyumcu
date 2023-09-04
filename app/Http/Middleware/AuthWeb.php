<?php

namespace App\Http\Middleware;

use Closure, Session;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource;

class AuthWeb
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Session::has('token')) {
            $user = User::where('token', Session::get('token'))->withCount('carts')->first();
            if ($user) {
                $request->merge(['user' => $user]);
                return $next($request);
            } else
                Session::forget('token');
        }
        return redirect('login');
    }
}
