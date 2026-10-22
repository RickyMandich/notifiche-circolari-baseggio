<?php

use App\Mail\NewCircularNotification;
use App\Models\Circular;
use App\Models\Subscriber;
use App\Services\WordpressApiService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

function fakeWordpressPosts(array $posts): void
{
    Http::fake([
        '*/wp-json/wp/v2/posts*' => Http::response($posts),
    ]);
}

function wpPost(int $id, string $title = 'Circolare n. 1', string $date = '2026-10-20T08:00:00'): array
{
    return [
        'id' => $id,
        'date_gmt' => $date,
        'link' => "https://www.icbaseggio.edu.it/circolare-{$id}/",
        'title' => ['rendered' => $title],
    ];
}

test('il service mappa e pulisce i dati di WordPress', function () {
    fakeWordpressPosts([wpPost(10, 'Circolare n. 5 &#8211; Uscita &amp; gita <b>scolastica</b>')]);

    $posts = app(WordpressApiService::class)->fetchLatestCirculars();

    expect($posts)->toHaveCount(1)
        ->and($posts[0]['wp_post_id'])->toBe(10)
        ->and($posts[0]['title'])->toBe('Circolare n. 5 – Uscita & gita scolastica')
        ->and($posts[0]['url'])->toBe('https://www.icbaseggio.edu.it/circolare-10/')
        ->and($posts[0]['published_at']->toDateTimeString())->toBe('2026-10-20 08:00:00');
});

test('alla prima esecuzione salva le circolari senza inviare email', function () {
    Mail::fake();
    Subscriber::factory()->count(2)->create();
    fakeWordpressPosts([wpPost(1), wpPost(2)]);

    $this->artisan('circulars:check')->assertSuccessful();

    expect(Circular::count())->toBe(2);
    Mail::assertNothingQueued();
});

test('le nuove circolari vengono salvate e notificate a tutti gli iscritti', function () {
    Mail::fake();
    Circular::factory()->create(['wp_post_id' => 1]);
    Subscriber::factory()->count(3)->create();
    fakeWordpressPosts([wpPost(2, 'Nuova circolare'), wpPost(1)]);

    $this->artisan('circulars:check')->assertSuccessful();

    expect(Circular::count())->toBe(2);
    $this->assertDatabaseHas('circulars', ['wp_post_id' => 2, 'title' => 'Nuova circolare']);
    Mail::assertQueued(NewCircularNotification::class, 3);
});

test('una circolare già nota non viene notificata due volte', function () {
    Mail::fake();
    Circular::factory()->create(['wp_post_id' => 1]);
    Subscriber::factory()->create();
    fakeWordpressPosts([wpPost(2), wpPost(1)]);

    $this->artisan('circulars:check')->assertSuccessful();
    $this->artisan('circulars:check')->assertSuccessful();

    expect(Circular::count())->toBe(2);
    Mail::assertQueued(NewCircularNotification::class, 1);
});

test('un errore del sito WordPress fa fallire il comando senza toccare il database', function () {
    Mail::fake();
    Http::fake(['*' => Http::response('errore', 500)]);

    $this->artisan('circulars:check')->assertFailed();

    expect(Circular::count())->toBe(0);
    Mail::assertNothingQueued();
});

test('la mail contiene titolo e link della circolare', function () {
    $circular = Circular::factory()->create([
        'title' => 'Circolare n. 9 - Sciopero',
        'url' => 'https://www.icbaseggio.edu.it/circolare-9/',
    ]);

    $mailable = new NewCircularNotification($circular);

    $mailable->assertHasSubject('Nuova circolare: Circolare n. 9 - Sciopero');
    $mailable->assertSeeInHtml('Circolare n. 9 - Sciopero');
    $mailable->assertSeeInHtml('https://www.icbaseggio.edu.it/circolare-9/');
});
