<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
<<<<<<< HEAD
use Illuminate\Support\Facades\Gate;
=======
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Tymon\JWTAuth\JWTGuard;
>>>>>>> 04eaf31 (first)

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Models' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
<<<<<<< HEAD

        //
=======
        Gate::define('admin',function($user){
            return in_array($user->role,['admin']);//管理员权限判断
        });
        Gate::define('editor',function($user){
            return in_array($user->role,['teacher']);
        });//教师权限判断

        Auth::extend('jwt', function ($app, $name, array $config) {
            return new JWTGuard(
                $app['tymon.jwt'],
                Auth::createUserProvider($config['provider']), // 确保此处正确解析 provider
                $app['request']
            );
        });
>>>>>>> 04eaf31 (first)
    }
}
