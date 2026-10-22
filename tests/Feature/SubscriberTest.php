<?php

use App\Models\Subscriber;
use App\Models\User;

test('gli ospiti vengono reindirizzati al login', function () {
    $this->get('/iscritti')->assertRedirect('/login');
    $this->post('/iscritti', ['name' => 'Mario', 'email' => 'mario@example.com'])->assertRedirect('/login');
    $this->delete('/iscritti/1')->assertRedirect('/login');
});

test('il login funziona con credenziali valide', function () {
    $user = User::factory()->create();

    $this->post('/login', ['email' => $user->email, 'password' => 'password'])
        ->assertRedirect('/iscritti');

    $this->assertAuthenticatedAs($user);
});

test('il login fallisce con credenziali errate', function () {
    $user = User::factory()->create();

    $this->post('/login', ['email' => $user->email, 'password' => 'sbagliata'])
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('un utente autenticato vede l\'elenco degli iscritti', function () {
    $subscriber = Subscriber::factory()->create();

    $this->actingAs(User::factory()->create())
        ->get('/iscritti')
        ->assertOk()
        ->assertSee($subscriber->email);
});

test('un utente autenticato può aggiungere un iscritto', function () {
    $this->actingAs(User::factory()->create())
        ->post('/iscritti', ['name' => 'Mario Rossi', 'email' => 'mario@example.com'])
        ->assertRedirect('/iscritti');

    $this->assertDatabaseHas('subscribers', ['email' => 'mario@example.com', 'name' => 'Mario Rossi']);
});

test('non si possono aggiungere email duplicate o non valide', function () {
    Subscriber::factory()->create(['email' => 'mario@example.com']);

    $this->actingAs(User::factory()->create())
        ->post('/iscritti', ['name' => 'Altro', 'email' => 'mario@example.com'])
        ->assertSessionHasErrors('email');

    $this->actingAs(User::factory()->create())
        ->post('/iscritti', ['name' => 'Altro', 'email' => 'non-una-email'])
        ->assertSessionHasErrors('email');

    expect(Subscriber::count())->toBe(1);
});

test('un utente autenticato può eliminare un iscritto', function () {
    $subscriber = Subscriber::factory()->create();

    $this->actingAs(User::factory()->create())
        ->delete("/iscritti/{$subscriber->id}")
        ->assertRedirect('/iscritti');

    $this->assertModelMissing($subscriber);
});

test('il logout termina la sessione', function () {
    $this->actingAs(User::factory()->create())
        ->post('/logout')
        ->assertRedirect('/login');

    $this->assertGuest();
});
