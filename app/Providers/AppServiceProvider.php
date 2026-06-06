<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

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
        Paginator::useBootstrapFive();
        \Illuminate\Support\Facades\View::composer(
            'layouts.sections.navbar.navbar',
            \App\Http\View\Composers\PendingRegistrationsComposer::class
        );

        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('Helper', \App\Helpers\Helpers::class);

        // Share menuData to all views
        $verticalMenuJson = file_get_contents(base_path('resources/menu/verticalMenu.json'));
        $verticalMenuData = json_decode($verticalMenuJson);
        $horizontalMenuJson = file_get_contents(base_path('resources/menu/horizontalMenu.json'));
        $horizontalMenuData = json_decode($horizontalMenuJson);

        \Illuminate\Support\Facades\View::share('menuData', [$verticalMenuData, $horizontalMenuData]);
    }
}
