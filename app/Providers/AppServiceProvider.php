<?php

namespace App\Providers;

use App\Models\TicketMaintenance;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->configurerRateLimiters();

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

    private function configurerRateLimiters(): void
    {
        RateLimiter::for('simulation-paiement', function (Request $request) {
            return Limit::perMinute(5)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('creation-ticket', function (Request $request) {
            return Limit::perMinute(5)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('message-ticket', function (Request $request) {
            return Limit::perMinute(15)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('signature-contrat', function (Request $request) {
            return Limit::perMinute(3)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('generation-quittance', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
        });
    }
}
