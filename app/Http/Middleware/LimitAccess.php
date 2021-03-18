<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class LimitAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(Auth::user()->groups_id && Auth::user()->group->type == 'admin'){
            return $next($request);
        }
        return redirect()->back()->with('flash_message','شما اجازه دسترسی به این صفحه را ندارید!');

    }
}
