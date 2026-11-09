<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Section extends Model
{
    protected $fillable = [
        'chapter_id',
        'title',
        'order',
        'word_count',
    ];

    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'word_count' => 'integer',
        ];
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    public function paragraphs(): HasMany
    {
        return $this->hasMany(Paragraph::class)->orderBy('order');
    }

    public function updateWordCount(): void
    {
        $this->word_count = $this->paragraphs()->sum('word_count');
        $this->saveQuietly();

        $this->chapter->updateWordCount();
    }

    /**
     * Normalize paragraph order values to be sequential starting from 0.
     * This ensures order values are always 0, 1, 2, 3, etc. with no gaps.
     */
    public function normalizeParagraphOrder(): void
    {
        $paragraphs = $this->paragraphs()->orderBy('order')->get();

        foreach ($paragraphs as $index => $paragraph) {
            if ($paragraph->order !== $index) {
                $paragraph->order = $index;
                $paragraph->saveQuietly();
            }
        }
    }
}
