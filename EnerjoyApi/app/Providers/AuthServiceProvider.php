<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define("human-resources", function ($user) {
            return in_array($user->job_id, [1, 2]);
        });

        Gate::define("read-conversation", function ($user, $conversation) {
            return $user->id == $conversation->employee_one_id || $user->id == $conversation->employee_two_id;
        });
    }
}
