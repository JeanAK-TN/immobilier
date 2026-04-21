<?php

namespace App\Http\Controllers\Locataire;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $contrat = $request->user()?->locataire?->contratCourant()?->load('bien');

        return view('locataire.dashboard', [
            'contrat' => $contrat,
        ]);
    }
}
