<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageContent extends Model
{
    protected $fillable = ['key', 'content'];

    protected function casts(): array
    {
        return ['content' => 'array'];
    }

    /**
     * Ambil konten halaman berdasarkan key, digabung dengan default.
     */
    public static function getContent(string $key, array $defaults = []): array
    {
        $row = static::where('key', $key)->first();

        return array_replace($defaults, $row?->content ?? []);
    }

    public static function saveContent(string $key, array $content): void
    {
        static::updateOrCreate(['key' => $key], ['content' => $content]);
    }

    /**
     * Ubah path gambar tersimpan menjadi URL publik.
     */
    public static function imageUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        return str_starts_with($path, 'http') ? $path : asset($path);
    }
}
