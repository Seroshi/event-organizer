<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use App\Enums\UserRole;

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

        

        Blade::if('master', function(){
            $role = auth()->user()?->role;
            return $role === UserRole::Master;
        });

        Blade::if('admin', function(){
            $role = auth()->user()?->role;
            return in_array($role, [UserRole::Master, UserRole::Admin]);
        });

        Blade::if('organizer', function(){
            $role = auth()->user()?->role;
            return in_array($role, [UserRole::Master, UserRole::Admin, UserRole::Organizer]);
        });

        Blade::if('user', function(){
            $role = auth()->user()?->role;
            return in_array($role, [UserRole::Master, UserRole::Admin, UserRole::Organizer, UserRole::User]);
        });
        
        // Change names to Dutch
        \Carbon\Carbon::setLocale('nl');
        setlocale(LC_TIME, 'nl_NL');
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
