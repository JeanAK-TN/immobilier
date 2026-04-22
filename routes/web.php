<?php

use App\Enums\Role;
use App\Http\Controllers\Locataire\DashboardController as LocataireDashboardController;
use App\Http\Controllers\Locataire\MonContratController;
use App\Http\Controllers\Locataire\PaiementController as LocatairePaiementController;
use App\Http\Controllers\Locataire\QuittanceController as LocataireQuittanceController;
use App\Http\Controllers\Locataire\TicketMaintenanceController as LocataireTicketMaintenanceController;
use App\Http\Controllers\Locataire\TicketMessageController as LocataireTicketMessageController;
use App\Http\Controllers\PasswordChangeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Proprietaire\BienController;
use App\Http\Controllers\Proprietaire\ContratController;
use App\Http\Controllers\Proprietaire\DashboardController as ProprietaireDashboardController;
use App\Http\Controllers\Proprietaire\LocataireController;
use App\Http\Controllers\Proprietaire\PaiementController as ProprietairePaiementController;
use App\Http\Controllers\Proprietaire\QuittanceController as ProprietaireQuittanceController;
use App\Http\Controllers\Proprietaire\TicketMaintenanceController as ProprietaireTicketMaintenanceController;
use App\Http\Controllers\Proprietaire\TicketMessageController as ProprietaireTicketMessageController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return match (auth()->user()->role) {
            Role::Proprietaire => redirect()->route('proprietaire.dashboard'),
            Role::Locataire => redirect()->route('locataire.dashboard'),
        };
    }

    return view('welcome');
})->name('home');

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
            Route::get('contrats/{contrat}/document', [ContratController::class, 'downloadDocument'])
                ->name('contrats.document');
            Route::resource('contrats', ContratController::class)->except('destroy');
            Route::resource('paiements', ProprietairePaiementController::class)->only(['index', 'show']);
            Route::get('quittances', [ProprietaireQuittanceController::class, 'index'])->name('quittances.index');
            Route::post('paiements/{paiement}/quittance', [ProprietaireQuittanceController::class, 'store'])
                ->name('quittances.store')
                ->middleware('throttle:generation-quittance');
            Route::get('quittances/{quittance}/telechargement', [ProprietaireQuittanceController::class, 'download'])->name('quittances.download');
            Route::resource('tickets', ProprietaireTicketMaintenanceController::class)->only(['index', 'show', 'update']);
            Route::post('tickets/{ticket}/messages', [ProprietaireTicketMessageController::class, 'store'])
                ->name('tickets.messages.store')
                ->middleware('throttle:message-ticket');
            Route::patch('locataires/{locataire}/activation', [LocataireController::class, 'toggleActivation'])
                ->name('locataires.activation');
            Route::resource('locataires', LocataireController::class)->except('destroy');
        });

    // Espace locataire
    Route::middleware('role:locataire')
        ->prefix('locataire')
        ->name('locataire.')
        ->group(function (): void {
            Route::get('/tableau-de-bord', LocataireDashboardController::class)->name('dashboard');
            Route::get('/mon-contrat', [MonContratController::class, 'show'])->name('contrat.show');
            Route::get('/mon-contrat/document', [MonContratController::class, 'downloadDocument'])->name('contrat.document');
            Route::put('/mon-contrat/signature', [MonContratController::class, 'sign'])
                ->name('contrat.sign')
                ->middleware('throttle:signature-contrat');
            Route::resource('paiements', LocatairePaiementController::class)->only(['index', 'show']);
            Route::post('paiements', [LocatairePaiementController::class, 'store'])
                ->name('paiements.store')
                ->middleware('throttle:simulation-paiement');
            Route::get('quittances', [LocataireQuittanceController::class, 'index'])->name('quittances.index');
            Route::get('quittances/{quittance}/telechargement', [LocataireQuittanceController::class, 'download'])->name('quittances.download');
            Route::resource('tickets', LocataireTicketMaintenanceController::class)->only(['index', 'show']);
            Route::post('tickets', [LocataireTicketMaintenanceController::class, 'store'])
                ->name('tickets.store')
                ->middleware('throttle:creation-ticket');
            Route::post('tickets/{ticket}/messages', [LocataireTicketMessageController::class, 'store'])
                ->name('tickets.messages.store')
                ->middleware('throttle:message-ticket');
        });
});

require __DIR__.'/auth.php';
