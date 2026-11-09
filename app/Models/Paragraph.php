<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Paragraph extends Model
{
    protected $fillable = [
        'section_id',
        'content',
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

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function characters(): BelongsToMany
    {
        return $this->belongsToMany(Character::class)->withTimestamps();
    }

    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(Location::class)->withTimestamps();
    }

    public function references(): BelongsToMany
    {
        return $this->belongsToMany(Reference::class)->withTimestamps();
    }
}
