<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

use Laravel\Passport\Passport;


class AuthServiceProvider extends ServiceProvider
{    
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        
        $this->registerPolicies();
        Passport::routes();

        Passport::tokensExpireIn(now()->addDays(15));
        
        //$this->registerPolicies();

        Gate::define('parameters', function ($user) {
            $group = $user->group()->first();
            $gates = $group->gates()->where('key', 'parameters')->get();
            return count($gates)>0;
        });

        Gate::define('students', function ($user) {
            $group = $user->group()->first();
            $gates = $group->gates()->where('key', 'students')->get();
            return count($gates)>0;
        });

        Gate::define('users', function ($user) {
            $group = $user->group()->first();
            $gates = $group->gates()->where('key', 'users')->get();
            return count($gates)>0;
        });

        Gate::define('purchases', function ($user) {
            $group = $user->group()->first();
            $gates = $group->gates()->where('key', 'purchases')->get();
            return count($gates)>0;
        });

        Gate::define('marketers', function ($user) {
            $group = $user->group()->first();
            $gates = $group->gates()->where('key', 'marketers')->get();
            return count($gates)>0;
        });

        Gate::define('sale_suggestions', function ($user) {
            $group = $user->group()->first();
            $gates = $group->gates()->where('key', 'sale_suggestions')->get();
            return count($gates)>0;
        });

        Gate::define('supporters', function ($user) {
            $group = $user->group()->first();
            $gates = $group->gates()->where('key', 'supporters')->get();
            return count($gates)>0;
        });

        Gate::define('supervisor', function ($user) {
            $group = $user->group()->first();
            $gates = $group->gates()->where('key', 'supervisor')->get();
            return count($gates)>0;
        });
    }
}
