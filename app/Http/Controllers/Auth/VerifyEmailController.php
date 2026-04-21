<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        $destination = match ($request->user()->role) {
            Role::Proprietaire => route('proprietaire.dashboard', absolute: false),
            Role::Locataire => route('locataire.dashboard', absolute: false),
        };

        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended($destination.'?verified=1');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->intended($destination.'?verified=1');
    }
}
