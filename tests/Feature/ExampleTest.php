<?php

use App\Models\User;

it('shows the welcome page to unauthenticated users', function () {
    $response = $this->get('/');

    $response->assertSuccessful();
    $response->assertViewIs('welcome');
});

it('redirects authenticated proprietaire from root to their dashboard', function () {
    $proprietaire = User::factory()->proprietaire()->create(['must_change_password' => false]);

    $this->actingAs($proprietaire)
        ->get('/')
        ->assertRedirect(route('proprietaire.dashboard'));
});

it('redirects authenticated locataire from root to their dashboard', function () {
    $locataire = User::factory()->locataire()->create(['must_change_password' => false]);

    $this->actingAs($locataire)
        ->get('/')
        ->assertRedirect(route('locataire.dashboard'));
});
