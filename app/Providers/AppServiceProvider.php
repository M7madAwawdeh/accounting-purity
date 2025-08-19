<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Faker\Generator as FakerGenerator;
use Faker\Factory as FakerFactory;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(FakerGenerator::class, function () {
            return FakerFactory::create('ar_JO');
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
        
        if (!function_exists('getCurrencyName')) {
            function getCurrencyName($code, $getSymbol = false)
            {
                $currency = \App\Models\Currency::where('code', $code)->first();
                if (!$currency) {
                    return $code;
                }
                return $getSymbol ? $currency->symbol : $currency->name;
            }
        }

        if (!function_exists('getVoucherRouteName')) {
            function getVoucherRouteName($modelClass)
            {
                $baseName = strtolower(class_basename($modelClass));
                if ($baseName === 'paymentvoucher') {
                    return 'payment-vouchers';
                }
                if ($baseName === 'expensevoucher') {
                    return 'expense-vouchers';
                }
                return \Illuminate\Support\Str::plural($baseName);
            }
        }
    }
}
