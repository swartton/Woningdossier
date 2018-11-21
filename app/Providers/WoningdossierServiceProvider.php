<?php

namespace App\Providers;

use App\Http\ViewComposers\CooperationComposer;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Interest;
use Illuminate\Support\ServiceProvider;

class WoningdossierServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //view()->composer('cooperation.layouts.app',  CooperationComposer::class);
        //view()->composer('*',  CooperationComposer::class);

        \View::composer('cooperation.tool.includes.interested', function ($view) {
            $view->with('interests', Interest::orderBy('order')->get());
        });

        \View::composer('*', function ($view) {
            $view->with('inputSources', InputSource::orderBy('order', 'desc')->get());
        });

        \View::creator('*', CooperationComposer::class);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //\Log::debug(__METHOD__);

        $this->app->bind('Cooperation', function () {
            $cooperation = null;
            if (\Session::has('cooperation')) {
                $cooperation = Cooperation::find(\Session::get('cooperation'));
            }

            return $cooperation;
        });

        $this->app->bind('CooperationStyle', function () {
            $cooperationStyle = null;
            if (\Session::has('cooperation')) {
                $cooperation = Cooperation::find(\Session::get('cooperation'));
                if ($cooperation instanceof Cooperation) {
                    $cooperationStyle = $cooperation->style;
                }
            }

            return $cooperationStyle;
        });
    }
}
