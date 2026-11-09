<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Story extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'order',
        'word_count',
        'grammar_prompt',
    ];

    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'word_count' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class)->orderBy('order');
    }

    public function scratchpads(): HasMany
    {
        return $this->hasMany(Scratchpad::class)->orderBy('order');
    }

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    public function characters(): HasMany
    {
        return $this->hasMany(Character::class);
    }

    public function references(): HasMany
    {
        return $this->hasMany(Reference::class);
    }

    public function updateWordCount(): void
    {
        $this->word_count = $this->chapters()->sum('word_count');
        $this->saveQuietly();
    }
}
