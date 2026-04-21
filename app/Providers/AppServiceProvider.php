<?php

namespace App\Providers;

use App\Models\TicketMaintenance;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        View::composer('layouts.navigation', function ($view): void {
            $user = auth()->user();

            if (! $user) {
                $view->with('ticketsActifsCount', 0);

                return;
            }

            $ticketsActifsCount = $user->isProprietaire()
                ? TicketMaintenance::query()->pourProprietaire($user)->actif()->count()
                : TicketMaintenance::query()->pourLocataire($user)->actif()->count();

            $view->with('ticketsActifsCount', $ticketsActifsCount);
        });
    }
}
