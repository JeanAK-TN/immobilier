<?php

use App\Enums\Role;
use App\Http\Controllers\Locataire\DashboardController as LocataireDashboardController;
use App\Http\Controllers\PasswordChangeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Proprietaire\BienController;
use App\Http\Controllers\Proprietaire\DashboardController as ProprietaireDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return match (auth()->user()->role) {
            Role::Proprietaire => redirect()->route('proprietaire.dashboard'),
            Role::Locataire => redirect()->route('locataire.dashboard'),
        };
    }

    return redirect()->route('login');
});

// Changement de mot de passe forcé (auth, sans le middleware password.changed)
Route::middleware('auth')->group(function (): void {
    Route::get('/modifier-mot-de-passe', [PasswordChangeController::class, 'edit'])->name('password.change');
    Route::put('/modifier-mot-de-passe', [PasswordChangeController::class, 'update'])->name('password.change.update');
});

// Routes authentifiées — password.changed appliqué à tout
Route::middleware(['auth', 'password.changed'])->group(function (): void {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Espace propriétaire
    Route::middleware('role:proprietaire')
        ->prefix('proprietaire')
        ->name('proprietaire.')
        ->group(function (): void {
            Route::get('/tableau-de-bord', ProprietaireDashboardController::class)->name('dashboard');
            Route::resource('biens', BienController::class);
        });

    // Espace locataire
    Route::middleware('role:locataire')
        ->prefix('locataire')
        ->name('locataire.')
        ->group(function (): void {
            Route::get('/tableau-de-bord', LocataireDashboardController::class)->name('dashboard');
        });
});

require __DIR__.'/auth.php';
