<?php

namespace App\Providers;

use  Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
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
        Schema::defaultStringLength(191);
        view()->composer('layout.navbar', function($view)
        {
        $result=(DB::table('groupcarts')->where('user_id',Auth::id())->count())+(DB::table('solocarts')->where('user_id',Auth::id())->count())+(DB::table('hotelcarts')->where('user_id',Auth::id())->count());
        $view->with('variable', $result);
        });

        Validator::extend('greater_than', function($attribute, $value, $parameters, $validator) {
          $min_field = $parameters[0];
          $data = $validator->getData();
          $min_value = $data[$min_field];
          return $value > $min_value;
        });   

        Validator::replacer('greater_than', function($message, $attribute, $rule, $parameters) {
            $message="Godzina rozpoczęcia musi być większa niż zakończenia";
          return str_replace(':field', $parameters[0], $message);
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
