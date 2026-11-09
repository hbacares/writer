<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Location extends Model
{
    protected $fillable = ['story_id', 'name', 'description', 'image', 'status'];

    protected function casts(): array
    {
        return [
            'status' => 'string',
        ];
    }

    public function story(): BelongsTo
    {
        return $this->belongsTo(Story::class);
    }

    public function paragraphs(): BelongsToMany
    {
        return $this->belongsToMany(Paragraph::class)->withTimestamps();
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeAiCreated($query)
    {
        return $query->where('status', 'ai_created');
    }

    public function scopeNotRejected($query)
    {
        return $query->where('status', '!=', 'rejected');
    }
}
