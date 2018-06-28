<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Match;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        \Carbon\Carbon::setLocale('fr');
        \View::composer('*', function ($view) {
            $disabled = \Cache::rememberForever('disabled', function () {
                return Match::orderBy('date')->first()->date->lt( Carbon::now()->addHours(24)); 
            });
            $view->with('disabled', $disabled);           
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
