<?php

use App\Enums\Role;
use App\Models\User;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertSuccessful();
    $response->assertSee('Créer mon compte');
});

test('users can register from the frontend as proprietaires', function () {
    $response = $this->post('/register', [
        'name' => 'Jean Dupont',
        'email' => 'jean.dupont@example.test',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $user = User::query()->firstWhere('email', 'jean.dupont@example.test');

    $this->assertAuthenticatedAs($user);
    $this->assertModelExists($user);

    expect($user->role)->toBe(Role::Proprietaire)
        ->and($user->must_change_password)->toBeFalse()
        ->and($user->is_active)->toBeTrue();

    $response->assertRedirect(route('proprietaire.dashboard', absolute: false));
});
