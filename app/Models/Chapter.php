<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chapter extends Model
{
    protected $fillable = [
        'story_id',
        'title',
        'description',
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

    public function story(): BelongsTo
    {
        return $this->belongsTo(Story::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class)->orderBy('order');
    }

    public function updateWordCount(): void
    {
        $this->word_count = $this->sections()->sum('word_count');
        $this->saveQuietly();

        $this->story->updateWordCount();
    }
}
