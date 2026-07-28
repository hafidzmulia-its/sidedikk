<?php

namespace App\Models;

use App\Enums\EducationPostStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EducationPost extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'body',
        'cover_image_path',
        'status',
        'published_at',
        'is_demo_data',
    ];

    protected function casts(): array
    {
        return [
            'status' => EducationPostStatus::class,
            'published_at' => 'datetime',
            'is_demo_data' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', EducationPostStatus::Published)
            ->whereNotNull('published_at');
    }

    public function getCoverImageUrlAttribute(): string
    {
        if (! $this->cover_image_path) {
            return asset('brand/icon-512.png');
        }

        if (str_starts_with($this->cover_image_path, 'http://') || str_starts_with($this->cover_image_path, 'https://')) {
            return $this->cover_image_path;
        }

        return asset(ltrim($this->cover_image_path, '/'));
    }

    public function getDisplayExcerptAttribute(): string
    {
        return $this->sanitizeDemoPrefix($this->excerpt);
    }

    public function getDisplayBodyAttribute(): string
    {
        return $this->sanitizeDemoPrefix($this->body);
    }

    /**
     * @return array<int, array{type: string, title?: string|null, text?: string, items?: array<int, string>}>
     */
    public function getDisplayBodyBlocksAttribute(): array
    {
        $content = $this->display_body;

        if ($content === '') {
            return [];
        }

        $sections = preg_split("/\R{2,}/", trim($content)) ?: [];
        $blocks = [];

        foreach ($sections as $section) {
            $lines = array_values(array_filter(array_map(
                static fn (string $line): string => trim($line),
                preg_split("/\R/", trim($section)) ?: [],
            )));

            if ($lines === []) {
                continue;
            }

            $bulletItems = array_values(array_map(
                static fn (string $line): string => trim(preg_replace('/^[-*•]\s*/u', '', $line) ?? $line),
                array_filter($lines, static fn (string $line): bool => preg_match('/^[-*•]\s+/u', $line) === 1),
            ));

            if (count($bulletItems) === count($lines)) {
                $blocks[] = [
                    'type' => 'list',
                    'title' => null,
                    'items' => $bulletItems,
                ];

                continue;
            }

            if (count($lines) > 1 && count($bulletItems) === count($lines) - 1 && preg_match('/^[-*•]\s+/u', $lines[0]) !== 1) {
                $blocks[] = [
                    'type' => 'list',
                    'title' => rtrim($lines[0], ':'),
                    'items' => $bulletItems,
                ];

                continue;
            }

            $blocks[] = [
                'type' => 'paragraph',
                'text' => implode("\n", $lines),
            ];
        }

        return $blocks;
    }

    protected function sanitizeDemoPrefix(?string $value): string
    {
        $text = trim((string) $value);

        foreach ([
            'DEMO DATA - NOT FOR MEDICAL USE. ',
            "DEMO DATA - NOT FOR MEDICAL USE\n\n",
            'DEMO DATA - NOT FOR MEDICAL USE',
        ] as $prefix) {
            if (str_starts_with($text, $prefix)) {
                $text = trim(substr($text, strlen($prefix)));
            }
        }

        return $text;
    }
}
