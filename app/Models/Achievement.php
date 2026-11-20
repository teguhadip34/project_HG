<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Achievement extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'icon',
        'metric',
        'target',
        'xp_reward',
        'rarity',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'user_achievements'
        )->withPivot('unlocked_at')
         ->withTimestamps();
    }
}
