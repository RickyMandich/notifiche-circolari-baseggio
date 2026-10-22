<?php

namespace App\Models;

use Database\Factories\CircularFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Circular extends Model
{
    /** @use HasFactory<CircularFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'wp_post_id',
        'title',
        'url',
        'published_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'wp_post_id' => 'integer',
            'published_at' => 'datetime',
        ];
    }
}
