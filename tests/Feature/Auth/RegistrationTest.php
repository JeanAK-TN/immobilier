<?php

test('registration screen is not publicly accessible', function () {
    $response = $this->get('/register');

    $response->assertStatus(404);
});
