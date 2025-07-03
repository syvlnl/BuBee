<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentView; 
use Illuminate\Contracts\View\View; 

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
        FilamentView::registerRenderHook(
            'panels::body.end',
            fn (): View => view('vendor.filament.footer')
        );
    }

    protected $observers = [
        Transaction::class => [TransactionObserver::class], 
    ];

}
