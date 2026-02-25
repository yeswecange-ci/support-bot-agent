<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GameQuestion extends Model
{
    protected $fillable = [
        'game_id',
        'order',
        'text',
        'type',
        'options',
        'correct_answer',
    ];

    protected $casts = [
        'options' => 'array',
        'order'   => 'integer',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(GameAnswer::class, 'question_id');
    }
}
