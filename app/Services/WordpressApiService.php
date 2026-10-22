<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class WordpressApiService
{
    /**
     * Recupera gli ultimi post dal sito scolastico e li normalizza.
     *
     * @return list<array{wp_post_id: int, title: string, url: string, published_at: Carbon}>
     *
     * @throws RequestException se il sito risponde con un errore HTTP
     */
    public function fetchLatestCirculars(): array
    {
        $response = Http::acceptJson()
            ->timeout(15)
            ->retry(2, 500)
            ->get(config('services.wordpress.posts_url'), [
                'per_page' => config('services.wordpress.per_page'),
                'orderby' => 'date',
                'order' => 'desc',
                '_fields' => 'id,date_gmt,link,title',
            ])
            ->throw();

        return collect($response->json())
            ->filter(fn ($post) => isset($post['id'], $post['link']))
            ->map(fn (array $post) => $this->mapPost($post))
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $post
     * @return array{wp_post_id: int, title: string, url: string, published_at: Carbon}
     */
    private function mapPost(array $post): array
    {
        $rawTitle = $post['title']['rendered'] ?? '';
        $title = trim(html_entity_decode(strip_tags($rawTitle), ENT_QUOTES | ENT_HTML5, 'UTF-8'));

        return [
            'wp_post_id' => (int) $post['id'],
            'title' => Str::limit($title !== '' ? $title : '(senza titolo)', 255, ''),
            'url' => $post['link'],
            // date_gmt è in UTC ma senza indicatore di fuso orario
            'published_at' => isset($post['date_gmt'])
                ? Carbon::parse($post['date_gmt'], 'UTC')
                : now(),
        ];
    }
}
