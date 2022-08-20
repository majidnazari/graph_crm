<?php

namespace App\Resolvers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Contracts\Resolver;


use Log;

class UserResolver_test implements \OwenIt\Auditing\Contracts\UserResolver // implements Resolver
{
    //use \OwenIt\Auditing\Auditable;

    /**
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public static function resolve()
    {
        $guards = Config::get('audit.user.guards', [
            \config('auth.defaults.guard')
        ]);

        foreach ($guards as $guard) {
            try {
                $authenticated = Auth::guard($guard)->check();
            } catch (\Exception $exception) {
                continue;
            }

            if (true === $authenticated) {
                Log::info("user id is:". Auth::guard($guard)->user());
            }
        }
        
        return 17;
        //return auth()->guard('api')->user()->group->type; 
        $guards = config('audit.user.guards'); // I only removed the default array value
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return Auth::guard($guard)->user();
            }
        }
        return null;
    }
}
