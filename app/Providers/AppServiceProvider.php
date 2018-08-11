<?php

namespace App\Providers;

use App\Brand;
use App\Phonemodel;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //ja dodo, kad god se ucitava vju app.blade.php iz mobilni\resources\views\layouts, odradice se ova boot() funkcija i ovde ce se izvuci
        //varijable koje su mu potrebne da popuni navigaciju, vadimo za sada popularne brendove i popularne modele
        view()->composer('layouts.app', function($view){
          $view->with('popularbrands', Brand::whereNotNull('brojmodela')->orderBy('brojmodela', 'DESC')->skip(0)->take(5)->get())
               ->with('popularmodels', Phonemodel::where('year', 2014)->skip(0)->take(8)->get()); 
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
