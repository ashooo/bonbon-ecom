<?php

namespace App\Providers;

use App\Models\StoreSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

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
        View::composer('*', function ($view): void {
            $storeSettings = null;

            try {
                if (Schema::hasTable('store_settings')) {
                    $storeSettings = StoreSetting::query()->first();
                }
            } catch (\Throwable) {
                $storeSettings = null;
            }

            $view->with('storeSettings', $storeSettings);
        });
    }
}
