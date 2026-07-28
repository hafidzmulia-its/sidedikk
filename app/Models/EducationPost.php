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
