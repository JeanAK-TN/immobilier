<?php

namespace App\Http\Controllers;

use App\Models\JournalAudit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class PasswordChangeController extends Controller
{
    public function edit(): View
    {
        return view('auth.change-password');
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
            'must_change_password' => false,
        ]);

        JournalAudit::enregistrer('changement_mot_de_passe');

        return redirect()->route(
            $request->user()->isProprietaire()
                ? 'proprietaire.dashboard'
                : 'locataire.dashboard'
        )->with('status', 'Votre mot de passe a été modifié avec succès.');
    }
}
