<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Log;
class CheckValidIp
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
        if(env('VALIDADDRESS') === 'all') return $next($request);
        $validAddresses = explode(',', env('VALIDADDRESS'));
        if (!in_array($request->ip(), $validAddresses)  && $request->path() === 'graphql-playground')
            abort(403);
        return $next($request);
        
    }
}
