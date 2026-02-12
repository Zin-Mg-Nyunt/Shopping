<?php

namespace App\Providers;

use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        Gate::define('access-staff-management',function(User $user){
            return $user->role === 'super_admin' || $user->role === 'store_manager' || $user->role === 'customer_service';
        });
        Gate::define('access-super-admin',function(User $user){
            return $user->role === 'super_admin';
        });
        Gate::define('access-store-manager',function(User $user){
            return $user->role === 'store_manager' || $user->role === 'super_admin';
        });
        Gate::define('access-customer-service',function(User $user){
            return $user->role === 'customer_service' || $user->role === 'super_admin' || $user->role === 'store_manager';
        });
    }

    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null
        );
    }
}
