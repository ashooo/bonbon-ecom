<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\StoreSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
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

        Order::created(function (Order $order): void {
            try {
                $invoiceService = app(\App\Services\InvoiceService::class);
                $invoiceService->generateInvoice($order);
            } catch (\Throwable $exception) {
                // Invoice generation should not block checkout.
                Log::warning('Invoice generation skipped during order creation.', [
                    'order_id' => $order->id,
                    'message' => $exception->getMessage(),
                ]);
            }
        });
    }
}
