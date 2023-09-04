<?php

namespace App\Http\Middleware;

use Closure, Session;
use Illuminate\Http\Request;
use App\Models\User;

class AuthCommon
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
            if (($user->role == 0 && $user->admin) || $user->role == 1)
                return $next($request);
            else
                return redirect(config('const.redirect'));
        }
        return redirect('login');
    }
}
