<?php

namespace App\Console\Commands;

use App\Mail\NewCircularNotification;
use App\Models\Circular;
use App\Models\Subscriber;
use App\Services\WordpressApiService;
use Illuminate\Console\Command;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CheckNewCirculars extends Command
{
    protected $signature = 'circulars:check';

    protected $description = 'Controlla il sito della scuola, salva le nuove circolari e notifica gli iscritti';

    public function handle(WordpressApiService $wordpress): int
    {
        try {
            $posts = $wordpress->fetchLatestCirculars();
        } catch (RequestException $e) {
            Log::error('circulars:check - errore nel contattare il sito WordPress', [
                'message' => $e->getMessage(),
            ]);
            $this->error('Impossibile leggere le circolari dal sito: '.$e->getMessage());

            return self::FAILURE;
        }

        $known = Circular::query()
            ->whereIn('wp_post_id', array_column($posts, 'wp_post_id'))
            ->pluck('wp_post_id')
            ->all();

        $newPosts = collect($posts)
            ->reject(fn (array $post) => in_array($post['wp_post_id'], $known, true))
            ->sortBy('published_at')
            ->values();

        $subscribers = Subscriber::all();
        $notified = 0;

        foreach ($newPosts as $post) {
            $circular = Circular::firstOrCreate(
                ['wp_post_id' => $post['wp_post_id']],
                $post,
            );

            if (! $circular->wasRecentlyCreated) {
                continue;
            }

            foreach ($subscribers as $subscriber) {
                Mail::to($subscriber)->queue(new NewCircularNotification($circular));
                $notified++;
            }
        }

        $this->info("Nuove circolari: {$newPosts->count()}. Email in coda: {$notified}.");

        return self::SUCCESS;
    }
}
